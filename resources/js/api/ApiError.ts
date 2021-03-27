/**
 * Представляет собой непредвиденную ошибку с апи
 */
export class ApiError extends Error {
   public constructor(message: string, error: Error) {
      super(message);
      alert(message);
      console.error(error);
   }
}
