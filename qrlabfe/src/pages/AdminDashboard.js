import { useState, useEffect } from 'react';
import { useAuth } from '../context/AuthContext';
import apiClient from '../api';
import { QRCodeSVG } from 'qrcode.react'; // Library QR

export default function AdminDashboard() {
  const { user, logout } = useAuth();
  const [meeting, setMeeting] = useState(null);
  const [qrToken, setQrToken] = useState('');
  const [error, setError] = useState('');

  // Fungsi untuk memulai sesi baru
  const startMeeting = async () => {
    try {
      // Data dummy (nanti bisa kamu buat jadi form)
      const meetingData = {
        nama_sesi: 'Praktikum PBO',
        pertemuan_ke: 1,
        tanggal: '2025-10-31',
        start_at: '10:00',
        end_at: '12:00',
      };
      const { data } = await apiClient.post('/api/admin/meetings', meetingData);
      setMeeting(data.meeting);
      setQrToken(data.token);
    } catch (err) {
      setError('Gagal memulai sesi.');
    }
  };

  // Efek untuk me-refresh QR setiap 30 detik
  useEffect(() => {
    if (meeting) {
      const interval = setInterval(async () => {
        try {
          const { data } = await apiClient.get(`/api/admin/meetings/${meeting.id}/qr`);
          setQrToken(data.token);
          console.log('QR Token refreshed:', data.token);
        } catch (err) {
          console.error('Gagal refresh token');
        }
      }, 30000); // 30 detik

      return () => clearInterval(interval); // Cleanup interval
    }
  }, [meeting]);

  return (
    <div>
      <h2>Halo, Admin {user?.name}!</h2>
      <button onClick={logout}>Logout</button>
      <hr />

      {!meeting ? (
        <button onClick={startMeeting}>Mulai Sesi Praktikum Baru</button>
      ) : (
        <div>
          <h3>Sesi: {meeting.nama_sesi} (Pertemuan {meeting.pertemuan_ke})</h3>
          <p>Silakan scan QR di bawah ini:</p>
          {qrToken ? (
            <QRCodeSVG value={qrToken} size={256} />
          ) : (
            <p>Membuat QR Code...</p>
          )}
        </div>
      )}
      {error && <p style={{ color: 'red' }}>{error}</p>}
    </div>
  );
}