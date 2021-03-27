import { mGetByID } from '../../lib/main';
import { CertData, CertInfo, GeCades, PluginData } from '../../lib/GeCades';
import { ErrorModal } from '../ErrorModal';
import { Api } from '../../api/Api';
import { SignModal } from './SignModal';
import { FilesUtils } from '../../forms/files/FilesUtils';
import { GeFile, SignState } from '../../lib/GeFile';
import { LogicError } from '../../lib/LogicError';
import { UploadedFile } from '../../api/response-handlers/files/UploadResponseHandler';
import {
   SignValidationResponse,
   ValidationResult
} from '../../api/response-handlers/files/InternalSignatureValidationResponseHandler';
import { FileHashingResponse } from '../../api/response-handlers/files/HashResponseHandler';
import { FileChecker } from '../../forms/files/FileChecker';
import { FileFieldInfo } from '../../forms/files/FileBlock';

/**
 * Представляет собой модуль подписания файла
 */
export class SignCreate extends SignModal {

   /**
    * Объект модуля подписания
    *
    * @type {SignCreate}
    */
   private static instance: SignCreate;

   /**
    * Флаг указывающий проинициализирован ли плагин КриптоПро
    */
   private isPluginInitialized = false;

   /**
    * Флаг указывающий, что в данный момент идет процесс подписания
    */
   private isSigning = false;

   /**
    * Блок с информацией о версии плагина и криптопровайдера
    */
   private pluginInfo: HTMLElement;

   /**
    * Блок со списком сертификатов пользователя и
    * информацией о выбранном сертификате
    */
   private certs: HTMLElement;

   /**
    * Блок с сертификатами пользователя
    */
   private certList: HTMLElement;

   /**
    * Блок с описанием выбранного сертификата
    */
   private certInfo: HTMLElement;

   /**
    * Кнопка загрузки файла открепленной подписи
    */
   private uploadButton: HTMLElement;

   /**
    * Кнопка создания открепленной подписи
    */
   private createButton: HTMLElement;

   /**
    * Кнопка удаления открепленной подписи
    */
   private deleteButton: HTMLElement;

   /**
    * Кнопка "Подписать" для создания открепленной подписи
    * после выбора сертификата
    */
   private signButton: HTMLElement;

   /**
    * Кнопка отмены действия создания открепленной подписи
    */
   private cancelButton: HTMLElement;

   /**
    * Блок с кнопками подписания и отмены создания подписи
    */
   private actions: HTMLElement;

   /**
    * Инпут, в который загружается файл открепленной подписи
    */
   private externalSignInput: HTMLInputElement;

   /**
    * Файл, для которого открыт модуль подписания
    */
   private geFile: GeFile;

   /**
    * Доступные расширения для файлового поля
    */
   private allowableExtensions: string[];

   /**
    * Запрещенные символы в названии загружаемых файлов
    */
   private forbiddenSymbols: string[];

   /**
    * Максимальный размер файла доступный для загрузки
    */
   private maxFileSize: number | null;

   public static getInstance(): SignCreate {

      if (!this.instance) {
         this.instance = new SignCreate();
      }

      return this.instance;
   }

   private constructor() {
      super();

      this.pluginInfo = mGetByID('signPluginInfo');
      this.certs = mGetByID('signCerts');
      this.certInfo = mGetByID('signCertInfo');
      this.actions = mGetByID('signActions');

      this.handleOverlay();

      this.handleCreateSignButton();
      this.handleUploadSignButton();
      this.handleDeleteSignButton();

      this.handleCancelButton();
      this.handleSignButton();
   }

   /**
    * Закрывает модальное окно модуля подписания
    */
   protected closeModal(): void {
      if (!this.isSigning) {
         this.modal.setAttribute('data-opened', 'false');
         this.overlay.setAttribute('data-opened', 'false');

         this.createButton.dataset.displayed = 'false';
         this.uploadButton.dataset.displayed = 'false';
         this.deleteButton.dataset.displayed = 'false';

         this.hideInfoBlocks();
      }
   }

   /**
    * Скрывает информационные блоки модуля подписания
    */
   private hideInfoBlocks(): void {
      this.certs.dataset.displayed = 'false';
      this.pluginInfo.dataset.displayed = 'false';
      this.actions.dataset.displayed = 'false';
      this.validateInfo.dataset.displayed = 'false';
   }

   /**
    * Инициализирует плагин и отображает элементы для создания подписи
    * при нажатии на кнопку "Создать открепленную подпись"
    */
   private handleCreateSignButton(): void {
      this.createButton = mGetByID('signCreate');
      this.createButton.addEventListener('click', () => {

         if (!this.isPluginInitialized) {
            this.initializePlugin();
         } else {
            this.showCreateSignElements();
         }

      });
   }

   /**
    * Инициализирует плагин для подписания
    */
   private initializePlugin(): void {

      // Берем объект плагина
      GeCades.getCadesPlugin()
         // Получаем информацию о версии плагина
         .then(() => {
            return GeCades.getPluginData();
         })
         // Получаем сертификаты пользователя
         .then((pluginData: PluginData) => {
            SignCreate.putPluginData(pluginData);
            return GeCades.getCerts();
         })
         // Добавляем блок с сертификатами
         .then((certs: CertData[]) => {
            this.handleCertListSelect(certs);
            this.showCreateSignElements();
            this.isPluginInitialized = true;
         })
         .catch((exc: string) => {
            console.error(exc);
            // this.closeModal();
         });
   }

   /**
    * Добавляет в модальное окно информацию о версии плагина и криптопровайдера
    *
    * @param pluginData - объект с данными о плагине
    */
   private static putPluginData(pluginData: PluginData): void {
      mGetByID('signPluginVersion').textContent = pluginData.pluginVersion;
      mGetByID('signCspVersion').textContent = pluginData.cspVersion;
   }

   /**
    * Обрабатывает список сертификаторв пользователя
    *
    * @param certs - массив объектов, содержащих имя и отпечаток сертификата
    */
   private handleCertListSelect(certs: CertData[]): void {
      this.certList = mGetByID('signCertList');

      // Добавляем сертификаты на страницу
      this.fillCertList(certs);

      GeCades.setCertificatesList(this.certList);
   }

   /**
    * Заполняет список сертификатов
    *
    * @param certs - массив объектов, содержащих имя и отпечаток сертификата
    */
   private fillCertList(certs: CertData[]): void {

      certs.forEach(cert => {
         const certItem: HTMLElement = document.createElement('DIV');
         certItem.classList.add('sign-modal__cert');
         certItem.setAttribute('data-cert', '');
         certItem.setAttribute('data-thumb', cert.thumb);

         const certText: HTMLElement = document.createElement('SPAN');
         certText.textContent = cert.text;
         certText.classList.add('sign-modal__cert-text');

         certItem.appendChild(certText);
         this.certList.appendChild(certItem);

         certItem.addEventListener('click', () => this.selectCert(certItem));
      });
   }

   /**
    * Устанавливает выбранный сертификат,
    * добаляет информацию о выбранном сертификате
    *
    * @param certItem - элемент выбранного сертификата
    */
   private selectCert(certItem: HTMLElement): void {
      const selectedCert: HTMLElement | null = this.certList.querySelector('[data-cert][data-selected="true"]');
      if (selectedCert) {
         selectedCert.dataset.selected = 'false';
         selectedCert.removeAttribute('data-state');
      }

      certItem.dataset.selected = 'true';
      // При выборе сертификата получаем информацию о нем
      GeCades.getCertInfo()
         // Добавляем на страницу данные о выбранном сертификате
         .then((certInfo: CertInfo) => {
            this.fillCertInfo(certInfo, certItem);
         })
         .catch((exc: string) => {
            new LogicError(`Ошибка при получении информации о сертификате: ${exc}`);
         });
   }

   /**
    * Заполняет блок с информацией о выбранном сертификате
    *
    * @param certInfo - объект с информацией о сертификате
    * @param certItem - элемент выбранного сертификата
    */
   private fillCertInfo(certInfo: CertInfo, certItem: HTMLElement): void {

      mGetByID('signSubjectName').textContent = certInfo.subjectName;
      mGetByID('signIssuerName').textContent = certInfo.issuerName;
      mGetByID('signValidFromDate').textContent = GeCades.formattedDateTo_ddmmyyyy_hhmmss(certInfo.validFromDate);
      mGetByID('signValidToDate').textContent = GeCades.formattedDateTo_ddmmyyyy_hhmmss(certInfo.validToDate);

      const certMessage = mGetByID('signCertMessage')
      certMessage.textContent = certInfo.certMessage;
      certMessage.dataset.state = certInfo.certStatus ? 'valid' : 'invalid';
      certItem.dataset.state = certInfo.certStatus ? 'valid' : 'invalid';

      this.certInfo.dataset.displayed = 'true';
   }

   /**
    * Показывает элементы для подписания файла
    */
   private showCreateSignElements(): void {
      this.certs.dataset.displayed = 'true';
      this.pluginInfo.dataset.displayed = 'true';

      this.actions.dataset.displayed = 'true';

      this.uploadButton.dataset.displayed = 'false';
      this.createButton.dataset.displayed = 'false';
   }

   /**
    * Открывает окно загрузки файлов и загружает открепленные подписи
    * при нажатии на кнопку "Загрузить открепленную подпись"
    */
   private handleUploadSignButton(): void {
      this.uploadButton = mGetByID('signUpload');
      this.uploadButton.addEventListener('click', () => {
         // Если не подписывается в данный момент, открываем окно загрузки файла
         if (!this.isSigning && !this.geFile.isInternalSign()) {
            this.externalSignInput.click();
         }
      });

      this.externalSignInput = mGetByID('signExternal');
      this.externalSignInput.addEventListener('change', () => {

         if (
            this.externalSignInput.files
            && this.externalSignInput.files.length === 1
            && !this.geFile.isInternalSign()
         ) {
            // Загружаем и проверяем открепленную подпись
            const signFile: File = this.externalSignInput.files.item(0)!;
            if (
               FileChecker.getInstance().checkFiles(
                  [signFile],
                  this.allowableExtensions,
                  this.forbiddenSymbols,
                  this.maxFileSize
               )
            ) {
               this.isSigning = true;
               this.sendSign(signFile);
            }
         }

         // Удаляем загруженные в инпут файлы
         this.externalSignInput.value = '';
      });
   }

   /**
    * Загружает и валидирует открепленную подпись
    *
    * @param signFile - файл открепленной подписи
    */
   private sendSign(signFile: File): void {
      let sign: UploadedFile;

      const fieldInfo: FileFieldInfo = this.geFile.getFileBlock().getFieldInfo();

      Api.uploadFiles(fieldInfo, [signFile])
         .then((uploadedSigns: UploadedFile[]) => {
            sign = uploadedSigns[0];
            return Api.checkFile(sign.starPath);
         })
         .then(() => {
            return Api.externalSignatureValidate(this.geFile.getStarPath(), sign.starPath)
         })
         .then((response: SignValidationResponse) => {
            this.handleSuccessSigning(sign.starPath, response.validationResult)
         })
         .catch(() => {
            this.isSigning = false;
         });
   }

   /**
    * Обрабатывает успешную валидацию открепленной подписи
    *
    * @param signStarPath - star path подписи
    * @param validationResult - результат валидации
    */
   private handleSuccessSigning(signStarPath: string, validationResult: ValidationResult): void {
      this.geFile.addExternalSign(signStarPath, validationResult);
      this.fillSignsInfo(validationResult);
      this.hideSignCreationElements();
      this.isSigning = false;
   }

   /**
    * Скрывает элементы для подписания в модальном оке
    */
   private hideSignCreationElements(): void {
      this.certs.dataset.displayed = 'false';
      this.actions.dataset.displayed = 'false';
      this.pluginInfo.dataset.displayed = 'false';

      this.createButton.dataset.displayed = 'false';
      this.uploadButton.dataset.displayed = 'false';
      this.deleteButton.dataset.displayed = 'true';
   }

   /**
    * Удаляет открепленную подпись при нажатии на кнопку "Удалить подпись"
    */
   private handleDeleteSignButton(): void {
      this.deleteButton = mGetByID('signDelete');
      this.deleteButton.addEventListener('click', () => {

         this.geFile.removeSigns();

         this.validateInfo.dataset.displayed = 'false';
         this.deleteButton.dataset.displayed = 'false';
         this.createButton.dataset.displayed = 'true';
         this.uploadButton.dataset.displayed = 'true';

      });
   }

   /**
    * Скрывает элементы для создания подписи при нажатии на кнопку "Отмена"
    */
   private handleCancelButton(): void {
      this.cancelButton = mGetByID('signCancel');
      this.cancelButton.addEventListener('click', () => {

         this.uploadButton.dataset.displayed = 'true';
         this.createButton.dataset.displayed = 'true';

         this.hideInfoBlocks();
      });

   }

   /**
    * Создает открепленную подпись при нажатии на кнопку "Подписать"
    */
   private handleSignButton(): void {
      this.signButton = mGetByID('signButton');
      this.signButton.addEventListener('click', () => {

         if (!this.isSigning && !this.geFile.isInternalSign()) {
            if (GeCades.getSelectedCertificateFromGlobalMap()) {
               this.isSigning = true;
               this.createSign();
            } else {
               ErrorModal.open('Ошибка при подписании файла', 'Не выбран сертификат');
            }
         }
      });

   }

   /**
    * Создает файл открепленной подписи и загружает его на сервер
    */
   private createSign(): void {
      let selectedAlgorithm: string;

      GeCades.getSelectedCertificateAlgorithm()
         .then((algorithm: string) => {

            selectedAlgorithm = algorithm;
            return Api.getFileHash(selectedAlgorithm, this.geFile.getStarPath());

         })
         .then((response: FileHashingResponse) => {
            return GeCades.getSignHash(selectedAlgorithm, response.hash);
         })
         .then((signHash: string) => {

            const signBlob = new Blob([signHash], {type: 'text/plain'});
            const file = new File([signBlob], `${this.geFile.getName()}.sig`);
            this.sendSign(file);

         })
         .catch(exc => {
            this.isSigning = false;
            this.geFile.setSignState(SignState.NotSigned);

            console.error(exc);
         });

   }

   /**
    * Открывает модуль подписания
    *
    * @param geFile - файл, для которого открывается модуль подписания
    */
   public open(geFile: GeFile): void {
      this.modal.setAttribute('data-opened', 'true');
      this.overlay.setAttribute('data-opened', 'true');

      this.geFile = geFile;
      this.addFileElement(this.geFile);
      this.putFileFieldData();

      const validationResults: ValidationResult | null = FilesUtils.getValidationResult(geFile);
      if (!validationResults) {

         this.createButton.dataset.displayed = 'true';
         this.uploadButton.dataset.displayed = 'true';

      } else {

         this.fillSignsInfo(validationResults);

         if (!geFile.isInternalSign()) {
            this.deleteButton.dataset.displayed = 'true';
         }

      }

   }

   /**
    * Получает данные о файловом поле для которого открыт модуль подписания
    */
   private putFileFieldData(): void {
      const fileField: HTMLElement = this.geFile.getParentField();

      this.allowableExtensions = FileChecker.getFileFieldAllowableExtensions(fileField);
      this.forbiddenSymbols = FileChecker.getFileFieldForbiddenSymbols(fileField);
      this.maxFileSize = FileChecker.getFileFieldMaxFileSize(fileField);
   }


}
