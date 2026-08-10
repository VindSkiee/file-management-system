import { onMounted, ref } from 'vue'

const DARK_MODE_KEY = 'fms_dark_mode'

function applyDark(dark: boolean): void {
  document.documentElement.classList.toggle('dark', dark)
}

function getInitialDark(): boolean {
  const stored = localStorage.getItem(DARK_MODE_KEY)

  if (stored !== null) {
    return stored === 'true'
  }

  return window.matchMedia('(prefers-color-scheme: dark)').matches
}

// Diterapkan saat module di-import (sebelum mount) agar tidak ada flash mode terang.
const isDark = ref(getInitialDark())
applyDark(isDark.value)

export function useDarkMode() {
  function toggleDarkMode(): void {
    isDark.value = !isDark.value
    applyDark(isDark.value)
    localStorage.setItem(DARK_MODE_KEY, String(isDark.value))
  }

  onMounted(() => {
    const media = window.matchMedia('(prefers-color-scheme: dark)')

    media.addEventListener('change', (event) => {
      // Hanya ikuti sistem jika user belum pernah memilih secara manual.
      if (localStorage.getItem(DARK_MODE_KEY) === null) {
        isDark.value = event.matches
        applyDark(isDark.value)
      }
    })
  })

  return { isDark, toggleDarkMode }
}
