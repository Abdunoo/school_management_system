import router from '@/router';
import axios from 'axios';

// Create a reactive loading state

const getToken = () => {
  return localStorage.getItem('token'); 
};

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    'Content-Type': 'application/json',
  }
});

// Request interceptor to start loading
apiClient.interceptors.request.use(
  (config) => {
    const token = getToken();
    if (!config.headers.skipToken && token) {
      config.headers['Authorization'] = `Bearer ${token}`;
    }
    
    if (config.method === 'put' && config.data instanceof FormData) {
      config.method = 'post';
      config.data.append('_method', 'PUT');
    }
    
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor to stop loading
apiClient.interceptors.response.use(
  (response) => {
    return response.data;
  },
  (error) => {
    if (error.response && error.response.status === 401) {
      router.push('/login');
    }
    return Promise.reject(error);
  }
);

export default apiClient;