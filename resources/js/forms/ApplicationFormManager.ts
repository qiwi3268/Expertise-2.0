import { jst, safeMapSetter } from '../lib/main';
import { Form, FormBlock, FormJSON } from './blocks/FormBlock';
import { ApplicationFormBlock } from './blocks/ApplicationFormBlock';
import { ApplicationTemplateBlocksManager } from './template-blocks/ApplicationTemplateBlocksManager';
import { FileBlocksManager, FileBox } from './files/FileBlocksManager';

type PageForm = {
   formParts: Map<string, Form>;
}

type ApplicationForm = {
   [fieldName: string]: string | FormJSON | FormJSON[] | FileBox
}

export class ApplicationFormManager {

   private static instance: ApplicationFormManager;

   private readonly pageForm: PageForm;

   public static getInstance(): ApplicationFormManager {
      if (!ApplicationFormManager.instance) {
         ApplicationFormManager.instance = new ApplicationFormManager();
      }

      return ApplicationFormManager.instance;
   }

   private constructor() {

      this.pageForm = {
         formParts: new Map<string, FormBlock>(),
      }

   }

   public initPageForm(): void {

      const forms: NodeListOf<HTMLElement> = document.querySelectorAll('[data-form]');
      forms.forEach(form => {
         const formBlock: ApplicationFormBlock = new ApplicationFormBlock(form);
         formBlock.initInnerFields();

         safeMapSetter(this.pageForm.formParts, formBlock.getName(), formBlock);
      })

   }

   public getApplicationForm(): ApplicationForm {
      const applicationForm: ApplicationForm = {};

      applicationForm['applicationId'] = jst.targetDocumentId;

      this.pageForm.formParts.forEach(formPart => {
         Object.assign(applicationForm, formPart.toJSON())
      });


      const templateBlocksForm: FormJSON = ApplicationTemplateBlocksManager.getInstance().getTemplateBlocksForm();
      Object.assign(applicationForm, templateBlocksForm);


      applicationForm['fileBox'] = FileBlocksManager.getInstance().getFilesForm();

      console.log(applicationForm);

      return applicationForm;
   }


}

