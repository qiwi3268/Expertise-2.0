import { GeFile, ValidationState } from '../../lib/GeFile';
import { isDisplayedElement, mClosest, mQS, safeDataAttrGetter, safeMapGetter } from '../../lib/main';
import { FileNeedsManager } from '../../lib/FileNeedsManager';
import { FileBlockJSON } from './FileBlocksManager';
import { LogicError } from '../../lib/LogicError';

/**
 * Описывает данные, идентифицирующие файловый блок
 */
export type FileFieldInfo = {
   mappings: string,
   structureNodeId?: number
}

/**
 * Представляет собой поле для файла, либо блок с документацией
 */
export class FileBlock {

   /**
    * Блок поля с файлами
    */
   private readonly element: HTMLElement;

   /**
    * Элемент поля формы, к которому относится файловый блок
    */
   private readonly parentField: HTMLElement;

   /**
    * Флаг, указывающий, что в файловом поле возможен только просмотр подписей
    */
   private readonly readOnly: boolean;

   /**
    * Файлы, которые относятся к полю
    */
   private files: Set<GeFile> = new Set<GeFile>();

   /**
    * Маппинги файлового блока
    */
   private readonly mappings: string;

   /**
    * Минимальный валидный статут подписи
    */
   private readonly minColor: string;

   /**
    * Идентификатор раздела документации
    */
   private readonly structureNodeId: number;

   /**
    * Инпут, в который записывается, добавлены ли файлы в блок
    */
   private resultInput: HTMLInputElement;

   /**
    * Создает объект файлового поля
    *
    * @param element - элемент файлового поля
    */
   public constructor(element: HTMLElement) {
      this.element = element;

      this.parentField = mClosest('[data-field]', this.element);
      this.resultInput = mQS('[data-field-result]', this.parentField);

      this.mappings = safeDataAttrGetter('name', this.parentField);
      this.minColor = safeDataAttrGetter('minColor', this.parentField);

      this.readOnly = this.parentField.hasAttribute('data-read-only');

      // Если файловый блок относится к документации
      const node: HTMLElement | null = this.parentField.querySelector('[data-structure-node-id]');
      if (node) {
         this.structureNodeId = parseInt(safeDataAttrGetter('structureNodeId', node));
      }

   }

   /**
    * Добавляет файл в массив файлов поля
    *
    * @param geFile - файл, относящийся к полю
    */
   public addFile(geFile: GeFile): void {
      // добавлять в зависимости от результата валидации

      FileNeedsManager.getInstance().addFile(geFile);
      this.files.add(geFile);
      this.resultInput.value = '1';

      //==================
   }

   /**
    * Удаляет файл из хранилища
    *
    * @param geFile - файл, относящийся к полю
    */
   public removeFile(geFile: GeFile): void {
      this.files.delete(geFile);
      if (this.files.size === 0) {
         this.resultInput.value = '';
      }
   }

   /**
    * Получает родительское поле формы
    */
   public getField(): HTMLElement {
      return this.parentField;
   }

   /**
    * Определяет, является ли файловый блок только для чтения
    */
   public isReadOnly(): boolean {
      return this.readOnly
   }

   /**
    * Получает маппинги блока
    */
   public getMappings(): string {
      return this.mappings;
   }

   /**
    * Получает файлы, загруженные в блок
    */
   public getFiles(): Set<GeFile> {
      return this.files;
   }

   /**
    * Определяет, относится ли блок к документации
    */
   public isDocumentation(): boolean {
      return !!this.structureNodeId;
   }

   /**
    * Получает информацию идентифицирующую файловый блок
    */
   public getFieldInfo(): FileFieldInfo {
      let fileFieldInfo: FileFieldInfo = {
         mappings: this.mappings
      }

      if (this.isDocumentation()) {
         fileFieldInfo.structureNodeId = this.structureNodeId;
      }

      return fileFieldInfo;
   }

   /**
    * Получает элемент с файлами
    */
   public getFilesContainer(): HTMLElement {
      return this.element;
   }

   /**
    * Определяет, существует ли файловое поле на странице
    */
   public isDisplayed(): boolean {
      return isDisplayedElement(this.element)
   }

   /**
    * Преобразует файловый блок в объект JSON
    */
   public toJSON(): FileBlockJSON {
      const form: FileBlockJSON = {
         result: null,
         starPaths: []
      };

      if (this.files.size > 0 && this.isDisplayed()) {
         form.starPaths = Array.from(this.files).map(geFile => geFile.getStarPath());
         form.result = Array.from(this.files).every(geFile => this.isValidFileColor(geFile));
      }

      return form;
   }

   /**
    * Определяет содержит ли дочерний файд минимальный статус подписи
    * @param geFile - дочерний файл
    */
   private isValidFileColor(geFile: GeFile): boolean {
      let isValidColor = false;

      switch (this.minColor) {
         case ValidationState.Green:
            isValidColor = geFile.getValidationState() === ValidationState.Green;
            break;
         case ValidationState.Orange:
            isValidColor = geFile.getValidationState() !== ValidationState.Red;
            break;
         case ValidationState.Red:
            isValidColor = true;
            break;
         default:
            new LogicError('Не определен минимальный цвет файлового поля');
      }

      return isValidColor;
   }

}
