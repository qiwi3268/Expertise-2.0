
export class PageScrollManager {

   public static handlePageScroll(): void {

   }

   private static disableScroll(): void {
      let body = document.body;
      body.style.overflow = 'hidden';
      body.style.paddingRight = '10px';
      // window.addEventListener('wheel', preventDefault, { passive: false });
   }

   private static enableScroll(): void {
      let body = document.body;
      body.style.overflow = '';
      body.style.paddingRight = '';
      // window.removeEventListener('wheel', preventDefault);
   }
}

