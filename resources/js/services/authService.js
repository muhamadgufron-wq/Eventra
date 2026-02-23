import api from './api'

export default {
    login: (credentials) => api.post('/login', credentials),
    register: (data) => api.post('/register', data),
    logout: () => api.post('/logout'),
    getUser: () => api.get('/user'),
    getCsrfCookie: () => api.get('/sanctum/csrf-cookie', { baseURL: '' }),
}
