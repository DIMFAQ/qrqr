import { useState } from 'react';
import { useAuth } from '../context/AuthContext';
import apiClient from '../api';
import QrScanner from 'react-qr-scanner'; // Library Scanner

export default function PraktikanDashboard() {
  const { user, logout } = useAuth();
  const [scanResult, setScanResult] = useState('');
  const [error, setError] = useState('');
  const [isScanning, setIsScanning] = useState(true);

  const handleScan = async (data) => {
    if (data && isScanning) {
      setIsScanning(false); // Stop scanning setelah dapat hasil
      const token = data.text;
      try {
        const response = await apiClient.post('/api/praktikan/attend', { token });
        setScanResult(`BERHASIL! Status: ${response.data.status}`);
        setError('');
      } catch (err) {
        setError(err.response.data.message || 'Scan Gagal!');
        // Izinkan scan ulang setelah 5 detik
        setTimeout(() => setIsScanning(true), 5000);
      }
    }
  };

  const handleError = (err) => {
    console.error(err);
    setError('Error kamera. Coba refresh halaman.');
  };

  return (
    <div style={{ width: '100%' }}>
      <h2>Halo, Praktikan {user?.name}! (NPM: {user?.praktikan?.npm})</h2>
      <button onClick={logout}>Logout</button>
      <hr />

      <h3>Scan QR Presensi di Sini:</h3>
      {isScanning ? (
        <QrScanner
          delay={300}
          onError={handleError}
          onScan={handleScan}
          style={{ width: '300px' }}
        />
      ) : (
        <h2>Scan Diterima...</h2>
      )}

      {scanResult && <p style={{ color: 'green', fontWeight: 'bold' }}>{scanResult}</p>}
      {error && <p style={{ color: 'red' }}>{error}</p>}
    </div>
  );
}