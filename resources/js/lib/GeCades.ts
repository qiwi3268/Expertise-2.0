import './cadesplugin_api'
import { ErrorModal } from '../modals/ErrorModal';
import { mQS, safeMapGetter } from './main';


const cadesplugin: any = (window as any).cadesplugin;

type GeneratorValue = {
   [key: string]: any;
}

/**
 * Описывает объект с версиями плагина и криптопровайдера
 */
export type PluginData = {
   /**
    * Версия плагина КриптоПро
    */
   pluginVersion: string,

   /**
    * Версия криптоконтейнера
    */
   cspVersion: string,
}

/**
 * Описывает объект с информацией о сертификате
 */
export type CertData = {
   /**
    * Владелец сертификата и дата выдачи
    */
   text: string,

   /**
    * Отпечаток сертификата
    */
   thumb: string
}

/**
 * Описывает объект с детальной информацией о сертификате
 */
export type CertInfo = {
   /**
    * Владелец
    */
   subjectName: string,

   /**
    * Издатель
    */
   issuerName: string,

   /**
    * Дата выдачи
    */
   validFromDate: Date,

   /**
    * Срок действия
    */
   validToDate: Date,

   /**
    * Описание статуса валидности
    */
   certMessage: string,

   /**
    * Статус валидности
    */
   certStatus: boolean,
}

/**
 * Класс предназначен для работы с плагином КриптоПРО
 */
export class GeCades {

   /**
    * Блок с сертификатами пользователя
    */
   private static certificatesList: HTMLElement;

   /**
    * Контейнер с парами вида: отпечаток сертификата - объект сертификата
    */
   private static globalCertsMap: Map<any, any> = new Map();

   /**
    * Возвращает объект плагина
    */
   public static getCadesPlugin(): any {
      return cadesplugin;
   }

   /**
    * Возвращает промис с объектом с версиями плагина и криптопровайдера
    */
   public static getPluginData(): Promise<PluginData> {
      const errorTitle = 'Ошибка при инициализации плагина КриптоПро';

      return new Promise<PluginData>((resolve, reject) => {

         const canAsync = !!cadesplugin.CreateObjectAsync;
         if (canAsync) {

            cadesplugin.async_spawn(function* () {

               let about: GeneratorValue = {};
               const pluginData: PluginData = {
                  pluginVersion: '',
                  cspVersion: '',
               };

               try {
                  about = yield cadesplugin.CreateObjectAsync('CAdESCOM.About');
               } catch (exc) {
                  reject('Ошибка при создании объекта About: ' + cadesplugin.getLastError(exc));
               }

               let currentPluginVersion: GeneratorValue = {};
               try {
                  currentPluginVersion = yield about.PluginVersion;  // Версия плагина
               } catch (exc) {
                  ErrorModal.open(errorTitle, 'Ошибка при получении версии плагина');
                  reject('Ошибка при получении версии плагина');
               }

               let currentCSPVersion: GeneratorValue = {};
               try {
                  currentCSPVersion = yield about.CSPVersion('', 80); // Версия криптопровайдера
               } catch (exc) {
                  ErrorModal.open(errorTitle, 'Отсутствует криптопровайдер');
                  reject('Отсутствует криптопровайдер');
               }

               try {
                  pluginData.pluginVersion = (yield currentPluginVersion.toString());
               } catch (exc) {
                  ErrorModal.open(errorTitle, 'Отстутствует плагин КриптоПро');
                  reject('Отстутствует плагин КриптоПро');
               }

               try {
                  const majorVersion: string = yield currentCSPVersion.MajorVersion;
                  const minorVersion: string = yield currentCSPVersion.MinorVersion;
                  const buildVersion: string = yield currentCSPVersion.BuildVersion;
                  pluginData.cspVersion = majorVersion + '.' + minorVersion + '.' + buildVersion;
               } catch (exc) {
                  ErrorModal.open(errorTitle, 'Ошибка при получении версии криптопровайдера');
                  reject('Ошибка при получении версии криптопровайдера');
               }

               resolve(pluginData);
            });

         } else {
            ErrorModal.open(errorTitle, 'Браузер не соответствует требованиям АИС (отсутствует поддержка async)');
            reject('Браузер не соответствует требованиям АИС (отсутствует поддержка async)');
         }

      });
   }

   /**
    * Возвращает промис с объектом содержащим массив с данными сертификатов пользователя
    */
   public static getCerts(): Promise<CertData[]> {
      return new Promise<CertData[]>((resolve, reject) => {

         cadesplugin.async_spawn(function* () {

            let store: GeneratorValue;

            try {

               store = yield cadesplugin.CreateObjectAsync('CAdESCOM.Store');

               if (!store) {
                  reject('Ошибка при создании хранилища сертификатов');
                  return;
               }

               yield store.Open();

            } catch (exc) {
               reject('Хранилище сертификатов недоступно ' + cadesplugin.getLastError(exc));
               return;
            }

            let certs: GeneratorValue = {};
            const certsData: CertData[] = [];
            let certsCount = 0;

            try {

               certs = yield store.Certificates;
               certsCount = yield certs.Count;

            } catch (exc) {
               reject('Ошибка при получении Certificates или Count: ' + cadesplugin.getLastError(exc));
            }

            if (certsCount === 0) {
               ErrorModal.open(
                  'Ошибка при получении списка сертификатов',
                  'Хранилище сертификатов пусто'
               );
               reject('Хранилище сертификатов пусто');
            }

            // Перебор сертификатов
            for (let i = 1; i <= certsCount; i++) {

               let cert: GeneratorValue = {};
               const certData: CertData = {
                  text: '',
                  thumb: ''
               };

               try {
                  cert = yield certs.Item(i);
               } catch (exc) {
                  reject('Ошибка при перечислении сертификатов: ' + cadesplugin.getLastError(exc));
               }

               let validFromDate: Date; // Дата выдачи
               let validToDate: Date;   // Срок действия
               let subjectName: string;    // Владелец

               try {

                  validFromDate = new Date(yield cert.ValidFromDate);
                  validToDate = new Date(yield cert.ValidToDate);
                  subjectName = yield cert.SubjectName;

               } catch (exc) {
                  ErrorModal.open('Ошибка при получении свойства ValidFromDate / ValidToDate / SubjectName', cadesplugin.getLastError(exc));
                  continue;
               }

               let hasPrivateKey = false; // Привязка сертификата к закрытому ключу

               try {
                  hasPrivateKey = yield cert.HasPrivateKey();
               } catch (exc) {
                  ErrorModal.open('Ошибка при получении свойства HasPrivateKey', cadesplugin.getLastError(exc));
               }

               // Берем только действительные сертификаты и с привязкой к закрытому ключу
               // if(new Date() < valid_to_date && hasPrivateKey) {
               if (new Date() < validToDate) {

                  certData.text = GeCades.getName(subjectName) + ', Выдан: ' + GeCades.formattedDateTo_ddmmyyyy(validFromDate);

               } else {
                  continue;
               }

               try {

                  // Отпечаток подписи
                  const thumbprint: string = yield cert.Thumbprint;
                  certData.thumb = thumbprint;
                  GeCades.addCertificateToGlobalMap(thumbprint, cert);


               } catch (exc) {
                  ErrorModal.open('Ошибка при получении свойства Thumbprint', cadesplugin.getLastError(exc));
                  continue;
               }


               certsData.push(certData);

            }

            yield store.Close();

            if (certsData.length === 0) {
               reject('Отсутствуют сертификаты');
            }

            resolve(certsData);

         });

      });
   }

   /**
    * Возвращает промис с объектом содержащим детальную информацию о сертификате
    */
   public static getCertInfo(): Promise<CertInfo> {
      return new Promise<CertInfo>((resolve, reject) => {

         const certificate = GeCades.getSelectedCertificateFromGlobalMap();

         cadesplugin.async_spawn(function* () {

            let subjectName: string; // Владелец
            let issuerName: string; // Издатель
            let validFromDate: Date; // Дата выдачи
            let validToDate: Date; // Срок действия

            try {

               // subject_name = GeCades.extractCN(yield certificate.SubjectName);
               subjectName = GeCades.getName(yield certificate.SubjectName);
               issuerName = GeCades.extractCN(yield certificate.IssuerName);
               validFromDate = new Date(yield certificate.ValidFromDate);
               validToDate = new Date(yield certificate.ValidToDate);

            } catch (exc) {
               reject(`Ошибка при получении свойства SubjectName / IssuerName / ValidFromDate / ValidToDate ${cadesplugin.getLastError(exc)}`);
               return
            }

            let validator: GeneratorValue;
            let isValid: boolean | undefined = undefined; // В случае неизвестного алгоритма

            // Если попадется сертификат с неизвестным алгоритмом, то
            // тут будет исключение. В таком сертификате просто такое поле
            try {
               validator = yield certificate.IsValid();
               isValid = yield validator.Result;
            } catch (exc) {

            }

            let hasPrivateKey = true; // Привязка сертификата к закрытому ключу

            try {
               hasPrivateKey = yield certificate.HasPrivateKey();
            } catch (exc) {
               ErrorModal.open('Ошибка при получении свойства HasPrivateKey', cadesplugin.getLastError(exc));
            }

            const now = new Date();
            let certMessage;
            let certStatus = false;

            if (!validFromDate || !validToDate) {
               certMessage = 'Отсутствуют сроки действия';
            } else if (now < validFromDate) {
               certMessage = 'Срок действия не наступил';
            } else if (now > validToDate) {
               certMessage = 'Срок действия истек';
            } else if (!hasPrivateKey) {
               certMessage = 'Нет привязки к закрытому ключу';
            } else if (isValid === false) {
               certMessage = 'Ошибка при проверке цепочки сертификатов';
            } else if (isValid === undefined) {
               certMessage = 'Сертификат с неизвестным алгоритмом';
            } else {
               certMessage = 'Действителен';
               certStatus = true;
            }

            const certInfo: CertInfo = {
               subjectName: subjectName,
               issuerName: issuerName,
               validFromDate: validFromDate,
               validToDate: validToDate,
               certMessage: certMessage,
               certStatus: certStatus
            }

            resolve(certInfo);
         }, certificate);

      });
   }

   /**
    * Возвращает промис с хэшэм подписи
    *
    * @param hashAlgorithm - алгоритм хэширования
    * @param hashValue - хэш файла
    */
   public static getSignHash(hashAlgorithm: string, hashValue: string): Promise<string> {

      return new Promise<any>((resolve, reject) => {

         const certificate = GeCades.getSelectedCertificateFromGlobalMap();

         cadesplugin.async_spawn(function* () {

            let signature: string;
            try {

               let signer: GeneratorValue;

               try {
                  signer = yield cadesplugin.CreateObjectAsync('CAdESCOM.CPSigner');
               } catch (exc) {
                  throw 'Ошибка при создании объекта CPSigner: ' + exc.number;
               }

               // Создаем объект CAdESCOM.HashedData
               const hashedData: GeneratorValue = yield cadesplugin.CreateObjectAsync('CAdESCOM.HashedData');

               //yield hashedData.propset_DataEncoding(cadesplugin.CADESCOM_BASE64_TO_BINARY);
               const algorithm = GeCades.getAlgorithmByValue(hashAlgorithm);
               yield hashedData.propset_Algorithm(algorithm);
               yield hashedData.SetHashValue(hashValue);

               // Атрибуты усовершенствованной подписи
               const signingTimeAttr: GeneratorValue = yield cadesplugin.CreateObjectAsync('CAdESCOM.CPAttribute');

               yield signingTimeAttr.propset_Name(cadesplugin.CAPICOM_AUTHENTICATED_ATTRIBUTE_SIGNING_TIME);
               yield signingTimeAttr.propset_Value(new Date());
               const attr: GeneratorValue = yield signer.AuthenticatedAttributes2;
               yield attr.Add(signingTimeAttr);

               const documentNameAttr: GeneratorValue = yield cadesplugin.CreateObjectAsync('CAdESCOM.CPAttribute');
               yield documentNameAttr.propset_Name(cadesplugin.CADESCOM_AUTHENTICATED_ATTRIBUTE_DOCUMENT_NAME);
               yield documentNameAttr.propset_Value('Document Name');
               yield attr.Add(documentNameAttr);

               if (signer) {
                  yield signer.propset_Certificate(certificate);
               } else {
                  throw 'Ошибка при добавлении атрибутов к объекту CPSigner';
               }

               const oSignedData: GeneratorValue = yield cadesplugin.CreateObjectAsync('CAdESCOM.CadesSignedData');
               yield oSignedData.propset_ContentEncoding(cadesplugin.CADESCOM_BASE64_TO_BINARY);

               yield signer.propset_Options(cadesplugin.CAPICOM_CERTIFICATE_INCLUDE_WHOLE_CHAIN);

               try {
                  signature = yield oSignedData.SignHash(hashedData, signer, cadesplugin.CADESCOM_CADES_BES);
               } catch (exc) {
                  throw 'Ошибка при создании подписи: ' + cadesplugin.getLastError(exc);
               }

            } catch (exc) {
               reject(exc);
               return;
            }
            resolve(signature);
         });
      })
   }

   /**
    * Возвращает промис с алгоритмом хэширования
    */
   public static getSelectedCertificateAlgorithm(): Promise<string> {

      return new Promise<any>((resolve, reject) => {

         cadesplugin.async_spawn(function* () {

            const selectedCertificate = GeCades.getSelectedCertificateFromGlobalMap();

            try {

               const publicKey: GeneratorValue = yield selectedCertificate.PublicKey();
               const algorithm: GeneratorValue = yield publicKey.Algorithm;
               const algorithmValue: string = yield algorithm.Value;

               resolve(algorithmValue);
            } catch (exc) {
               reject(exc);
            }
         });
      })

   }

   /**
    * Геттер и сеттер блока с сертификатами
    */
   public static setCertificatesList(certList: HTMLElement): void {
      GeCades.certificatesList = certList;
   }

   private static getCertificatesList(): HTMLElement {
      if (!GeCades.certificatesList) {
         ErrorModal.open('Ошибка при получении списка сертификатов', 'Отсутствует блок с сертификатами');
      }
      return GeCades.certificatesList;
   }

   private static addCertificateToGlobalMap(thumbprint: any, cert: any) {
      GeCades.globalCertsMap.set(thumbprint, cert);
   }

   public static getSelectedCertificateFromGlobalMap() {

      if (!('globalCertsMap' in GeCades)) {
         ErrorModal.open('Ошибка при получении сертификата', 'Не удалось получить хранилище сертификатов');
         return null;
      }

      const list = GeCades.getCertificatesList();
      const selectedCert: HTMLElement = mQS('[data-cert][data-selected="true"]', list);

      // Не выбран сертификат
      if (!selectedCert) {
         return null
      }

      return safeMapGetter(GeCades.globalCertsMap, selectedCert.dataset.thumb);
   }

   /**
    * Возвращает код алгоритма в зависимости от значения в открытом ключе
    *
    * @param value - значение алгоритма
    */
   private static getAlgorithmByValue(value: string): number | null {
      switch (value) {
         case '1.2.643.7.1.1.1.1' :
            return cadesplugin.CADESCOM_HASH_ALGORITHM_CP_GOST_3411_2012_256; // 101
         case '1.2.643.7.1.1.1.2' :
            return cadesplugin.CADESCOM_HASH_ALGORITHM_CP_GOST_3411_2012_512; // 102
         case '1.2.643.2.2.19'    :
            return cadesplugin.CADESCOM_HASH_ALGORITHM_CP_GOST_3411;          // 100
         default :
            return null;
      }
   }

   /**
    * Возвращает ФИО или организацию владельца сертификата
    *
    * @param subjectName - строка с полными данными о владельце сертификата
    * @return Строка с ФИО или организацией
    */
   private static getName(subjectName: string): string {
      let sn = '';
      let g = '';
      let name: string;

      const indexSn: number = subjectName.indexOf('SN=', 0);
      if (indexSn !== -1) {
         const snSep: number = subjectName.indexOf(',', indexSn);
         sn = `${subjectName.substring(indexSn + 3, snSep)}`;
      }

      const indexG: number = subjectName.indexOf('G=', 0);
      if (indexG !== -1) {
         const gSep: number = subjectName.indexOf(',', indexG);
         g = `${subjectName.substring(indexG + 2, gSep)}`;
      }

      if (sn.length === 0) {
         let cn = '';
         const indexCn: number = subjectName.indexOf('CN=', 0);
         if (indexCn !== -1) {
            const cnSep: number = subjectName.indexOf(',', indexCn);
            cn = `${subjectName.substring(indexCn + 3, cnSep)}`;
         }

         name = `${cn}`;
      } else {
         name = `${sn} ${g}`;
      }

      return name;
   }

   /**
    * Возвращает организцаию владельца сертификата
    *
    * @param subjectName - строка с полными данными о владельце сертификата
    * @return Строка с организацией
    */
   private static extractCN(subjectName: string): string {

      const indCn: number = subjectName.indexOf('CN', 0);
      const sep: number = subjectName.indexOf(',', indCn);

      return subjectName.slice(indCn, sep);
   }

   /**
    * Возвращает форматированную дату из объекта Date в формате дд.мм.гггг
    *
    * @param date - объект даты
    * @return Дата в виде строки
    */
   private static formattedDateTo_ddmmyyyy(date: Date): string {
      const monthDate = GeCades.addZero(date.getDate() + 1);
      const month = GeCades.addZero(date.getMonth());
      return monthDate + '.' + month + '.' + date.getFullYear();
   }

   /**
    * Возвращает форматированную дату из объекта Date в формате дд.мм.гггг чч:мм:сс
    *
    * @param date - объект даты
    * @return Дата в виде строки
    */
   public static formattedDateTo_ddmmyyyy_hhmmss(date: Date): string {
      const first = GeCades.formattedDateTo_ddmmyyyy(date);
      const h = GeCades.addZero(date.getHours());
      const m = GeCades.addZero(date.getMinutes());
      const s = GeCades.addZero(date.getSeconds());
      return first + ' ' + h + ':' + m + ':' + s;
   }

   /**
    * Дополняет число нулем, если оно меньше 10
    *
    * @param num - число
    * @return Форматированное число
    */
   private static addZero(num: number): string | number {
      return (num < 10) ? '0' + num : num;
   }

}


