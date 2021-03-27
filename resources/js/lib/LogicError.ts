/**
 * Представляет собой логическую ошибку в коде
 */
export class LogicError extends Error {
   public constructor(message: string) {
      super(message);
      alert(message);
      console.error(this.stack);
   }
}
