import { FileNeedsManager } from './FileNeedsManager';

/**
 * Представляет собой подпись к файлу
 */
export class GeSign {

   /**
    * Путь к файлу подписи в файловой системе сервера в виде star path
    */
   private readonly starPath: string;

   /**
    * Сохранена ли подпись
    */
   private saved: boolean;

   /**
    * Удалена ли подпись
    */
   private deleted: boolean;

   public constructor(starPath: string) {
      this.starPath = starPath;
      this.saved = false;
      this.deleted = false;

      FileNeedsManager.getInstance().addSign(this);
   }

   /**
    * Устанавливает флаг удаления подписи
    *
    * @param deleted - удалена ли подпись
    */
   public setIsDeleted(deleted: boolean): void {
      this.deleted = deleted;
   }

   /**
    * Устанавливает флаг сохранения подписи
    *
    * @param saved - сохранена ли подпись
    */
   public setIsSaved(saved: boolean): void {
      this.saved = saved;
   }

   /**
    * Определяет, удалена ли подпись
    */
   public isDeleted(): boolean {
      return this.deleted;
   }

   /**
    * Определяет, сохранена ли подпись
    */
   public isSaved(): boolean {
      return this.saved;
   }

   /**
    * Возвращает star path подписи
    */
   public getStarPath(): string {
      return this.starPath;
   }

}
