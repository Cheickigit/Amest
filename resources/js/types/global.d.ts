export {}

declare global {
  interface Window {
    publicLayout?: () => {
      open: boolean
      scrolled: boolean
      progress: number
      phoneDisplay: string
      phoneHref: string
      whatsappHref: string
      email: string
      city: string
      init(): void
      toggle(): void
      close(): void
      onScroll(): void
      onMouse(e: MouseEvent): void
    }
  }

  // si tu utilises Ziggy globalement (sinon on l’importe dans le composant)
  // declare function route(name: string, params?: any, absolute?: boolean, config?: any): string
}
