import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from './context/AuthContext';

// Nanti kita buat halaman-halaman ini:
import LoginPage from './pages/LoginPage';
import RegisterPage from './pages/RegisterPage';
import AdminDashboard from './pages/AdminDashboard';
import PraktikanDashboard from './pages/PraktikanDashboard';
import LoadingPage from './pages/LoadingPage';
import NotFoundPage from './pages/NotFoundPage';

// Komponen untuk proteksi halaman
const ProtectedRoute = ({ children, role }) => {
  const { user, loading } = useAuth();

  if (loading) {
    return <LoadingPage />; // Tampilkan loading spinner
  }
  
  if (!user) {
    // Jika belum login, tendang ke halaman login
    return <Navigate to="/login" />;
  }
  
  // Jika butuh role spesifik (admin/praktikan)
  if (role && user.role !== role) {
     // Jika role tidak sesuai, tendang ke halaman utama
     return <Navigate to="/" />;
  }

  return children;
};

// Komponen untuk routing otomatis berdasarkan role
const HomeRedirect = () => {
  const { user, loading } = useAuth();
  if (loading) return <LoadingPage />;
  
  if (user.role === 'admin') return <Navigate to="/admin" />;
  if (user.role === 'praktikan') return <Navigate to="/praktikan" />;
  return <Navigate to="/login" />; // Fallback
};

function App() {
  return (
    <AuthProvider>
      <BrowserRouter>
        <Routes>
          {/* Rute Publik */}
          <Route path="/login" element={<LoginPage />} />
          <Route path="/register" element={<RegisterPage />} /> 

          {/* Rute Utama (otomatis redirect) */}
          <Route path="/" element={<ProtectedRoute><HomeRedirect /></ProtectedRoute>} />
          
          {/* Rute Khusus Admin */}
          <Route 
            path="/admin" 
            element={<ProtectedRoute role="admin"><AdminDashboard /></ProtectedRoute>} 
          />
          
          {/* Rute Khusus Praktikan */}
          <Route 
            path="/praktikan" 
            element={<ProtectedRoute role="praktikan"><PraktikanDashboard /></ProtectedRoute>} 
          />

          {/* Halaman 404 */}
          <Route path="*" element={<NotFoundPage />} />
        </Routes>
      </BrowserRouter>
    </AuthProvider>
  );
}
export default App;