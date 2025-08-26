// Shim minimal pour VS Code / TS avec Ziggy v2
declare module 'ziggy-js' {
  export const ZiggyVue: any
  export function route(
    name: string,
    params?: any,
    absolute?: boolean,
    config?: any
  ): any
}
