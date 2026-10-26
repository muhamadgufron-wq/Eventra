import api from './api'

const getPdf = (url) => api.get(url, { responseType: 'blob' })

export default {
    invoice: (id) => getPdf(`/invoices/${id}/pdf`),
    paidInvoice: (id) => getPdf(`/invoices/${id}/paid-pdf`),
    dpReceipt: (id) => getPdf(`/invoices/${id}/dp-receipt`),
    paymentReceipt: (id) => getPdf(`/payments/${id}/receipt`),
}
