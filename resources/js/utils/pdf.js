const getPdfFilename = (headers, fallback) => {
    const disposition = headers?.['content-disposition'] ?? ''
    const match = disposition.match(/filename="?([^";]+)"?/i)

    return match ? decodeURIComponent(match[1]) : fallback
}

const download = (blob, filename) => {
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')

    link.href = url
    link.download = filename
    document.body.appendChild(link)
    link.click()
    link.remove()
    setTimeout(() => URL.revokeObjectURL(url), 1000)
}

export const openPdfRequest = async (request, fallbackFilename) => {
    // Open synchronously so browsers do not treat the later PDF as a popup.
    const tab = window.open('', '_blank')

    try {
        const { data, headers } = await request()
        const url = URL.createObjectURL(data)
        const filename = getPdfFilename(headers, fallbackFilename)

        if (tab) {
            tab.location.href = url
        } else {
            download(data, filename)
        }

        setTimeout(() => URL.revokeObjectURL(url), 60000)
    } catch (error) {
        tab?.close()
        throw error
    }
}

export const pdfErrorMessage = async (error, fallback) => {
    const data = error?.response?.data

    if (data instanceof Blob) {
        try {
            const payload = JSON.parse(await data.text())

            if (payload.message) return payload.message
        } catch {
            // The response was not a JSON error body.
        }
    }

    return data?.message || error?.message || fallback
}
