import api from './api'

export default {
    getSummary: () => api.get('/dashboard/summary'),
}
