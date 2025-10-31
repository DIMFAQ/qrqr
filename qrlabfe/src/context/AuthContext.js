import { createContext, useState, useContext, useEffect } from 'react';
import apiClient from '../api';

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true); // Untuk cek session awal

  // Fungsi untuk mengambil data user (jika ada session aktif)
  const getUser = async () => {
    try {
      const { data } = await apiClient.get('/api/user');
      setUser(data);
    } catch (error) {
      setUser(null);
    } finally {
      setLoading(false);
    }
  };

  // Cek user saat aplikasi pertama kali load
  useEffect(() => {
    getUser();
  }, []);

  const login = async (email, password) => {
    // 1. Ambil CSRF Cookie (WAJIB untuk Sanctum SPA)
    await apiClient.get('/sanctum/csrf-cookie');
    
    // 2. Kirim request Login
    const { data } = await apiClient.post('/login', { email, password });
    
    // 3. Set data user ke state
    setUser(data);
  };

  const logout = async () => {
    await apiClient.post('/logout');
    setUser(null);
  };

    const register = async (name, email, npm, password, passwordConfirmation) => {
    // 1. Ambil CSRF Cookie
    await apiClient.get('/sanctum/csrf-cookie');
    
    // 2. Kirim request Register
    const { data } = await apiClient.post('/register', {
      name,
      email,
      npm,
      password,
      password_confirmation: passwordConfirmation, // Kirim konfirmasi
    });
    
    // 3. Set data user ke state (langsung login)
    setUser(data);
  };
  // --- BATAS KODE BARU ---

  return (
    <AuthContext.Provider value={{ user, login, logout, register, loading, getUser }}>
      {/* (Ganti 'register' di sini) */}
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);