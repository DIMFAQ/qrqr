import { useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { Navigate, Link } from 'react-router-dom';

export default function RegisterPage() {
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [npm, setNpm] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');
  const [error, setError] = useState('');
  const { user, register } = useAuth();

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');

    if (password !== passwordConfirmation) {
      setError('Konfirmasi password tidak cocok.');
      return;
    }

    try {
      await register(name, email, npm, password, passwordConfirmation);
      // Jika berhasil, AuthContext akan update dan <Navigate> di bawah akan jalan
    } catch (err) {
      // Ambil error validasi dari Laravel
      if (err.response && err.response.status === 422) {
        const errors = Object.values(err.response.data.errors).join(', ');
        setError(errors);
      } else {
        setError('Gagal registrasi.');
      }
    }
  };

  // Jika sudah login, tendang dari halaman register
  if (user) {
    return <Navigate to="/" />;
  }

  return (
    <div>
      <form onSubmit={handleSubmit}>
        <h2>Registrasi Praktikan Baru</h2>
        <div>
          <label>Nama Lengkap:</label>
          <input type="text" value={name} onChange={(e) => setName(e.target.value)} required />
        </div>
        <div>
          <label>Email:</label>
          <input type="email" value={email} onChange={(e) => setEmail(e.target.value)} required />
        </div>
        <div>
          <label>NPM:</label>
          <input type="text" value={npm} onChange={(e) => setNpm(e.target.value)} required />
        </div>
        <div>
          <label>Password:</label>
          <input type="password" value={password} onChange={(e) => setPassword(e.target.value)} required />
        </div>
        <div>
          <label>Konfirmasi Password:</label>
          <input type="password" value={passwordConfirmation} onChange={(e) => setPasswordConfirmation(e.target.value)} required />
        </div>
        {error && <p style={{ color: 'red' }}>{error}</p>}
        <button type="submit">Daftar</button>
      </form>
      <p>
        Sudah punya akun? <Link to="/login">Login di sini</Link>
      </p>
    </div>
  );
}