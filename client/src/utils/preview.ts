export type PreviewType = 'image' | 'pdf' | null

export function getPreviewType(fileName: string): PreviewType {
  const name = fileName.toLowerCase()

  if (/\.(png|jpe?g|gif|webp)$/.test(name)) return 'image'
  if (name.endsWith('.pdf')) return 'pdf'

  return null
}
