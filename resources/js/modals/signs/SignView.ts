/**
 * Представляет собой модуль просмотра подписей файла
 */
import { GeFile } from '../../lib/GeFile';
import { SignModal } from './SignModal';
import { FilesUtils } from '../../forms/files/FilesUtils';

export class SignView extends SignModal {

   /**
    * Объект модуля просмотра подписей
    */
   private static instance: SignView;

   /**
    * Предназначен для получения объекта модального окна
    * просмотра подписей файла
    */
   public static getInstance(): SignView {

      if (!this.instance) {
         this.instance = new SignView();
      }

      return this.instance;
   }

   /**
    * Создает объект модального окна просмотра подписей файла
    */
   private constructor() {
      super();
   }

   /**
    * Закрывает модальное окно просмотра подписей
    */
   protected closeModal(): void {
      this.modal.setAttribute('data-opened', 'false');
      this.overlay.setAttribute('data-opened', 'false');

      this.validateInfo.dataset.displayed = 'false';
   }

   /**
    * Добавляет файл и результаты проверки подписей и
    * открывает модуль просмотра подписей
    *
    * @param geFile - файл, для которого проматриваются результаты проверок
    */
   public open(geFile: GeFile): void {
      this.modal.setAttribute('data-opened', 'true');
      this.overlay.setAttribute('data-opened', 'true');

      this.addFileElement(geFile);

      const validationResults = FilesUtils.getValidationResult(geFile);
      if (validationResults) {
         this.fillSignsInfo(validationResults);
      }

   }

}

