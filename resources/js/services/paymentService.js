import api from './api'

export default {
    getAll: (params = {}) => api.get('/payments', { params }),
    create: (data) => api.post('/payments', data),
    delete: (id) => api.delete(`/payments/${id}`),
}
