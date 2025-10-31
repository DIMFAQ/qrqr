// File: qrlabfe/src/api.js

import axios from 'axios';

const apiClient = axios.create({
  // Gunakan 127.0.0.1 ini adalah alamat yang paling pasti
  baseURL: 'http://127.0.0.1:8000', 

  withCredentials: true,
});

export default apiClient;