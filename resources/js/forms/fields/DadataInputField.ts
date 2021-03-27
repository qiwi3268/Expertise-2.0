import { InputField } from './InputField';
import { addFieldMutationHandler, safeDataAttrGetter } from '../../lib/main';
import { FormBlock, StringForm } from '../blocks/FormBlock';
import { Dadata } from '../../api/response-handlers/dadata/OrganizationByOrgInnResponseHandler';
import { Api } from '../../api/Api';
import { LogicError } from '../../lib/LogicError';
import { ConfirmationModal } from '../../modals/ConfirmationModal';
import { FormsMapper, FormsMapperOptions } from '../utils/FormsMapper';
import { makeLogger } from 'ts-loader/dist/logger';

enum DatadataFields {
   OrgInn = 'orgInn',
   PersInn = 'persInn',
   Bik = 'bik'
}

export class DadataInputField extends InputField {

   protected dadataType: string;
   protected form: FormBlock;
   protected mappingOptions: FormsMapperOptions;

   protected getConfirmMessage: Function;
   protected findDataAction: Function;

   public constructor(element: HTMLElement, form: FormBlock) {
      super(element);

      this.form = form;
      this.dadataType = safeDataAttrGetter('dadata', this.element);
   }

   protected initValidation(): void {

      addFieldMutationHandler(this.element, () => {
         this.complete();

         if (this.mask.masked.isComplete) {
            this.fetchData();
         }

      });

   }

   protected fetchData(): void {

      let dadataGetter: Promise<Dadata>;

      switch (this.dadataType) {

         case DatadataFields.OrgInn:
            this.getConfirmMessage = this.getOrgConfirmMessageParts;
            this.findDataAction = this.confirm.bind(this);
            dadataGetter = Api.getOrganizationByOrgInn(this.getValue());
            this.mappingOptions = this.getOrgFormMapperOptions();
            break;
         case DatadataFields.PersInn:
            this.findDataAction = this.confirm.bind(this);
            this.getConfirmMessage = this.getPersConfirmMessageParts;
            dadataGetter = Api.getOrganizationByPersInn(this.getValue());
            this.mappingOptions = this.getPersFormMapperOptions();
            break;
         case DatadataFields.Bik:
            this.findDataAction = this.fillForm.bind(this);
            dadataGetter = Api.getBankByBik(this.getValue());
            break;
         default:
            new LogicError('Не определен тип поля для получения dadata');
            dadataGetter = new Promise<Dadata>((resolve, reject) => reject());
            break;
      }

      dadataGetter
         .then((response: Dadata) => {
            if (response.found) {
               this.findDataAction(response.data);
            }
         });
   }

   protected confirm(responseData: StringForm): void {
      ConfirmationModal.getInstance().open(
         this.getConfirmMessage(responseData),
         () => this.fillForm(responseData),
      );
   }

   protected fillForm(data: StringForm): void {

      this.mappingOptions !== undefined
         ? FormsMapper.copyFromStringForm(this.form, data, this.mappingOptions)
         : FormsMapper.copyFromStringForm(this.form, data);

   }

   protected getOrgConfirmMessageParts(data: StringForm): string[] {
      const parts: string[] = [];

      const orgName = data['fullName'];
      const directorFio = `${data['directorLastName']} ${data['directorFirstName']} ${data['directorMiddleName']}`;

      parts.push(`Получены данные об организации: ${orgName}`);
      parts.push(`Директор: ${directorFio}`);
      parts.push(`Заполнить форму?`);

      return parts;
   }

   protected getPersConfirmMessageParts(data: StringForm): string[] {
      const parts: string[] = [];

      const entrepreneurFio = `${data['lastName']} ${data['firstName']} ${data['middleName']}`;

      parts.push(`Получены данные об индивидуальном предпринимателе: ${entrepreneurFio}`);
      parts.push(`Заполнить форму?`);

      return parts;
   }


   protected getOrgFormMapperOptions(): FormsMapperOptions {
      return {
         ignore: ['orgInn'],
      }
   }

   protected getPersFormMapperOptions(): FormsMapperOptions {
      return {
         ignore: ['persInn'],
      }
   }
}



