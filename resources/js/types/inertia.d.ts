import '@inertiajs/vue3'

declare module '@inertiajs/vue3' {
  export interface PageProps {
    flash?: { success?: string; error?: string; info?: string }
    // ajoute ici d'autres props partagées si besoin (auth, etc.)
  }
}
