import { useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { Navigate, Link } from 'react-router-dom';

export default function LoginPage() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const { user, login } = useAuth();

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    try {
      await login(email, password);
      // Jika berhasil, AuthContext akan update dan <Navigate> di bawah akan jalan
    } catch (err) {
      setError('Gagal login. Cek email/password.');
    }
  };

  // Jika sudah login, tendang dari halaman login
  if (user) {
    return <Navigate to="/" />;
  }

  return (
    <div>
      <form onSubmit={handleSubmit}>
        <h2>Login Sistem Presensi QR</h2>
        <div>
          <label>Email:</label>
          <input type="email" value={email} onChange={(e) => setEmail(e.target.value)} required />
        </div>
        <div>
          <label>Password:</label>
          <input type="password" value={password} onChange={(e) => setPassword(e.target.value)} required />
        </div>
        {error && <p style={{ color: 'red' }}>{error}</p>}
        <button type="submit">Login</button>
      </form>

      <p>
        Belum punya akun praktikan? 
        <Link to="/register"> Daftar di sini</Link>
      </p>
      {/* --- BATAS KODE BARU --- */}
    </div>
  );
}