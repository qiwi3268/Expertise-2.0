import { jst, mGetByID } from '../../lib/main';
import { ApplicationMiscModalManager } from '../../modals/miscs/ApplicationMiscModalManager';
import { ApplicationTemplateBlocksManager } from '../../forms/template-blocks/ApplicationTemplateBlocksManager';
import { FileBlocksManager } from '../../forms/files/FileBlocksManager';
import { CalendarManager } from '../../forms/CalendarManager';
import { MiscDependenciesManager } from '../../modals/miscs/MiscDependenciesManager';
import { ApplicationRadioBlocksManager } from '../../forms/radio/ApplicationRadioBlocksManager';
import { ApplicationFormCardsManager } from '../../forms/cards/ApplicationFormCardsManager';
import { ApplicationFormManager } from '../../forms/ApplicationFormManager';
import { SingleDependenciesManager } from '../../dependencies/SingleDependenciesManager';
import { Dependencies, DependentAttributes, DependentElementSelectors } from '../../dependencies/DependenciesManager';
import { MultipleDependenciesManager } from '../../dependencies/MultipleDependenciesManager';
import { Cache, CacheSlots } from '../../lib/Cache';
import dependencies from '../../dependencies/application-form.json';
import { Api } from '../../api/Api';


document.addEventListener('DOMContentLoaded', () => {

   const saveButton: HTMLElement = mGetByID('saveApplication');
   saveButton.addEventListener('click', () => saveApplication());

   console.log(jst.targetDocumentId);
   console.log(jst);

   // Должен быть первым
   Cache.createSlot(CacheSlots.PageManagers);

   ApplicationMiscModalManager.create().initPageMiscModals();

   // ApplicationMiscModalManager.getInstance().initPageMiscModals();

   ApplicationTemplateBlocksManager.getInstance().initPageTemplateBlocks();

   FileBlocksManager.getInstance().initPageFileBlocks();
   CalendarManager.getInstance().initPageDateFields();


   Cache.createSlot(CacheSlots.MiscDependencies);
   MiscDependenciesManager.getInstance().initPageMainMiscs();

   // Инициализация зависимых предметов экспертизы должны быть
   // после инициализации главных справочников (исправить этот момент)
   ApplicationRadioBlocksManager.getInstance().initPageRadioBlocks();


   ApplicationFormCardsManager.getInstance().initPageCards();


   // Должны быть последними
   initPageDependencies();


   // Должно быть после инициализации зависимостей, чтобы изменение полей
   // срабатывало после изменения зависимостей
   ApplicationFormManager.getInstance().initPageForm();


   document.addEventListener('keydown', event => {
      if (event.ctrlKey) {

         // ApplicationMutableBlocksManager.getInstance().getMutableBlocksForm();
         // Api.saveApplicationForm(ApplicationFormManager.getInstance().getApplicationForm());

         /*const date = '2021-11-26 08:55:54 UTC';
         console.log(new Date(date));*/

         /*   let format = DateTimeFormat('ddMMYYYY');
            console.log(format.format(new Date(date)));*/

         /* console.log('cache');
          console.log('tut');
          console.log(Cache.createSlot('test' as CacheSlots));

          console.log(Cache.slot('test' as CacheSlots));*/

         // console.log(Cache.slot().slot());

         ApplicationFormManager.getInstance().getApplicationForm();


         // FileNeedsManager.getInstance().putFilesToFileNeeds();

         // Api.testAPI();


         /*  ConfirmationModal.getInstance().open(
              'Тестовое сообщение',
              () => console.log('confirm'),
              () => console.log('cancel')
           );
  */


         // let fileNeeds: any = FileNeedsManager.getInstance().putFilesToFileNeeds().getFileNeeds();
         // console.log(fileNeeds);
         /*         FileNeedsManager.getInstance().putFilesToFileNeeds();
                  console.log('//===================================');
                  console.log('filesToSave');
                  console.log(FileNeedsManager.getInstance().filesToSave);
                  console.log('filesToDelete');
                  console.log(FileNeedsManager.getInstance().filesToDelete);
                  console.log('signsToSave');
                  console.log(FileNeedsManager.getInstance().signsToSave);
                  console.log('signsToDelete');
                  console.log(FileNeedsManager.getInstance().signsToDelete);
                  console.log('//===================================');
                  FileNeedsManager.getInstance().clear();*/

         // console.log(Object.entries(obj));

         // console.log(Cache.getSlotValue(CacheSlots.Executors))

         // console.log(ApplicationMutableBlocksManager.getInstance().getMutableBlocks());

      }
   })


});

function initPageDependencies(): void {

   Cache.createSlot(CacheSlots.FieldDependencies);

   const singleDisplayDependencies = new SingleDependenciesManager(
      dependencies.singleDisplayDependencies,
      DependentElementSelectors.Block,
      DependentAttributes.Displayed
   );
   singleDisplayDependencies.initPageMainFields();
   Cache.slot(CacheSlots.FieldDependencies).set(Dependencies.SingledDisplay, singleDisplayDependencies);

   const multipleDisplayDependencies = new MultipleDependenciesManager(
      dependencies.multipleDisplayDependencies,
      DependentElementSelectors.Block,
      DependentAttributes.Displayed
   );
   multipleDisplayDependencies.initPageMainFields();
   Cache.slot(CacheSlots.FieldDependencies).set(Dependencies.MultipleDisplay, multipleDisplayDependencies);

   const multipleRequireDependencies = new MultipleDependenciesManager(
      dependencies.multipleRequireDependencies,
      DependentElementSelectors.Field,
      DependentAttributes.Required
   );
   multipleRequireDependencies.initPageMainFields();
   Cache.slot(CacheSlots.FieldDependencies).set(Dependencies.MultipleRequire, multipleRequireDependencies);

}


function saveApplication(): void {
   // console.log(ApplicationFormManager.getInstance().getApplicationForm());
   Api.saveApplicationForm(ApplicationFormManager.getInstance().getApplicationForm());
   // FileNeedsManager.getInstance().putFilesToFileNeeds();
   // console.log(FileNeedsManager.getInstance().getFileNeeds());
   // FileNeedsManager.getInstance().clear();

   // Api.sendFileNeeds();

}
