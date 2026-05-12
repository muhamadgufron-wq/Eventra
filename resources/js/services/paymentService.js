import api from './api'

export default {
    getAll: (params = {}) => api.get('/payments', { params }),
    get: (id) => api.get(`/payments/${id}`),
    create: (data) => api.post('/payments', data),
    update: (id, data) => api.put(`/payments/${id}`, data),
    delete: (id) => api.delete(`/payments/${id}`),
}
