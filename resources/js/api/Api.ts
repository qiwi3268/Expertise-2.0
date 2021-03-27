import { jst } from '../lib/main';
import { LogicError } from '../lib/LogicError';
import { MiscDependency } from '../modals/miscs/MiscDependenciesManager';
import { SaveApplicationFormResponseHandler } from './response-handlers/SaveApplicationFormResponseHandler';
import { FileNeedsManager } from '../lib/FileNeedsManager';
import { FileNeedsSetterResponseHandler } from './response-handlers/files/FileNeedsSetterResponseHandler';
import { UploadedFile, UploadResponseHandler } from './response-handlers/files/UploadResponseHandler';
import { CheckResponseHandler } from './response-handlers/files/CheckResponseHandler';
import {
   InternalSignatureValidationResponseHandler,
   SignValidationResponse
} from './response-handlers/files/InternalSignatureValidationResponseHandler';
import { ExternalSignatureValidationResponseHandler } from './response-handlers/files/ExternalSignatureValidationResponseHandler';
import { FileHashingResponse, HashResponseHandler } from './response-handlers/files/HashResponseHandler';
import { DependencyGetterResponseHandler } from './response-handlers/miscs/DependencyGetterResponseHandler';
import {
   Dadata,
   OrganizationByOrgInnResponseHandler
} from './response-handlers/dadata/OrganizationByOrgInnResponseHandler';
import { OrganizationByPersInnResponseHandler } from './response-handlers/dadata/OrganizationByPersInnResponseHandler';
import { FileFieldInfo } from '../forms/files/FileBlock';
import { BankByBikResponseHandler } from './response-handlers/dadata/BankByBikResponseHandler';

/**
 * Представляет собой класс для отправки запросов на API
 */
export class Api {

   /**
    * Отправляет файлы на апи загрузки файлов
    *
    * @param fieldData - данные о файловом поле
    * @param files - массив с загружаемыми файлами
    * @param uploadCallback - колбэк для обработки запроса в процессе отправки данных
    * @return {Promise<UploadedFile[]>}
    */
   public static uploadFiles(
      fieldData: FileFieldInfo,
      files: File[],
      uploadCallback: Function | null = null
   ): Promise<UploadedFile[]> {

      const formData = Api.getFileUploaderFormData(fieldData, files);
      const apiHandler = new UploadResponseHandler();
      return apiHandler
         .buildPostConfig('/api/files/upload', formData, uploadCallback)
         .send();
   }

   /**
    * Возвращает объект формы для отправки на api загрузки файлов
    *
    * @param fieldData - данные о файловом поле
    * @param files - массив с загружаемыми файлами
    * @return {FormData} формы
    */
   private static getFileUploaderFormData(fieldData: FileFieldInfo, files: File[]): FormData {
      const formData: FormData = new FormData();
      formData.append('targetDocumentId', jst.targetDocumentId);
      formData.append('mappings', fieldData.mappings);

      if (!!fieldData.structureNodeId) {
         formData.append('structureNodeId', String(fieldData.structureNodeId));
      }

      try {
         files.forEach(file => formData.append('files[]', file));
      } catch (exc) {
         throw new LogicError('В форму загрузки не передан массив файлов');
      }

      return formData;
   }

   /**
    * Отправляет путь файла на апи проверки файла
    *
    * @param starPath - star path файла
    */
   public static checkFile(starPath: string): Promise<void> {
      const formData: FormData = new FormData();
      formData.append('starPath', starPath);
      const apiHandler = new CheckResponseHandler();
      return apiHandler
         .buildPostConfig('/api/files/check', formData)
         .send();
   }

   /**
    * Отправляет путь файла на апи валидации встроенной подписи
    *
    * @param internalSignatureStarPath - star path файла
    */
   public static internalSignatureValidate(internalSignatureStarPath: string): Promise<SignValidationResponse> {
      const formData: FormData = new FormData();
      formData.append('internalSignatureStarPath', internalSignatureStarPath);
      const apiHandler = new InternalSignatureValidationResponseHandler();
      return apiHandler
         .buildPostConfig('/api/files/internalSignatureValidation', formData)
         .send();
   }

   /**
    * Отправляет путь файла и открепленной подписи на апи валидации открепленной подписи
    *
    * @param originalStarPath - star path файла
    * @param externalSignatureStarPath - star path открепленной подписи
    */
   public static externalSignatureValidate(
      originalStarPath: string,
      externalSignatureStarPath: string
   ): Promise<SignValidationResponse> {

      const formData: FormData = new FormData();
      formData.append('originalStarPath', originalStarPath);
      formData.append('externalSignatureStarPath', externalSignatureStarPath);
      const apiHandler = new ExternalSignatureValidationResponseHandler();
      return apiHandler
         .buildPostConfig('/api/files/externalSignatureValidation', formData)
         .send();
   }

   /**
    * Отправляет алгорим  хэширования и путь файла на апи получения хэша файла
    *
    * @param signAlgorithm - алгоритм хэширования
    * @param starPath - star path файла
    */
   public static getFileHash(signAlgorithm: string, starPath: string): Promise<FileHashingResponse> {
      const formData: FormData = new FormData();
      formData.append('signAlgorithm', signAlgorithm);
      formData.append('starPath', starPath);
      const apiHandler = new HashResponseHandler();
      return apiHandler
         .buildPostConfig('/api/files/hash', formData)
         .send();
   }

   /**
    * Отправляет наименование главного и зависимого полей и
    * выбранное значение главного поля на апи получения значений справочника
    *
    * @param mainAlias - наименование главного поля
    * @param subAliases - наименование зависимого поля
    * @param selectedItemId - значение главного поля
    */
   public static getMiscDependencies(mainAlias: string, subAliases: string, selectedItemId: number): Promise<MiscDependency[]> {

      const apiHandler = new DependencyGetterResponseHandler();
      const params = {
         mainMiscAlias: mainAlias,
         subMiscAliases: subAliases,
         selectedId: selectedItemId.toString(),
      };

      return apiHandler
         .buildGetConfig('/api/miscs/dependency', params)
         .send();
   }

   /**
    * Отправляет форму заявления на апи сохранения заявления
    *
    * @param form - форма заявления
    */
   public static saveApplicationForm(form: unknown): unknown {
      const apiHandler = new SaveApplicationFormResponseHandler();

      return apiHandler
         .buildPostJSONConfig('/api/formExpertiseApplicationSave', form)
         .send();
   }

   public static sendFileNeeds(): unknown {
      const fileNeeds: any = FileNeedsManager.getInstance().putFilesToFileNeeds().getFileNeeds();
      FileNeedsManager.getInstance().clear();

      const apiHandler = new FileNeedsSetterResponseHandler();

      return apiHandler
         .buildPostJSONConfig('/api/files/needs', fileNeeds)
         .send();
   }

   /**
    * Отправляет ИНН организации на апи получения данных об организации
    *
    * @param inn - ИНН организации
    */
   public static getOrganizationByOrgInn(inn: string): Promise<Dadata> {

      const apiHandler = new OrganizationByOrgInnResponseHandler();
      const params = {orgInn: inn};

      return apiHandler
         .buildGetConfig(`/api/dadata/organizationByOrgInn`, params)
         .send();
   }

   /**
    * Отправляет ИНН индивидуального предприниматея на апи получения данных о предпринимателе
    *
    * @param inn - ИНН индивидуального предпренимателя
    */
   public static getOrganizationByPersInn(inn: string): Promise<Dadata> {
      const apiHandler = new OrganizationByPersInnResponseHandler();
      const params = {persInn: inn};

      return apiHandler
         .buildGetConfig(`/api/dadata/organizationByPersInn`, params)
         .send();
   }

   /**
    * Отправляет БИК банка на апи получения данных о банке
    *
    * @param bik - БИК банка
    */
   public static getBankByBik(bik: string): Promise<Dadata> {
      const apiHandler = new BankByBikResponseHandler();
      const params = {bik: bik};

      return apiHandler
         .buildGetConfig(`/api/dadata/bankByBik`, params)
         .send();
   }
}


