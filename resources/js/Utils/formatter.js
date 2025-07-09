export function formatCurrency(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value || 0)
}

export function parseCurrency(value) {
    return Number(String(value).replace(/[^0-9]/g, ''))
}