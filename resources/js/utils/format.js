export const formatCurrency = (val) => {
    if (val === null || val === undefined || val === '') return 'Rp 0'
    const num = Math.round(parseFloat(val))
    if (isNaN(num)) return 'Rp 0'
    
    const formatted = new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(num)
    return `Rp ${formatted}`
}

export const formatNumber = (val) => {
    if (val === null || val === undefined || val === '') return '0'
    const num = Math.round(parseFloat(val))
    if (isNaN(num)) return '0'
    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(num)
}

export const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    })
}
