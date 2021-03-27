import { htmlArrDecode, isNumeric, mQS, safeDataAttrGetter, safeMapGetter } from '../../lib/main';
import { MiscItem } from './modals/MiscModal';
import { Cache, CacheSlots } from '../../lib/Cache';
import { MiscDependenciesManager, MiscDependency } from './MiscDependenciesManager';
import { Api } from '../../api/Api';

/**
 * !!! Переделать этот класс !!!
 */

/**
 * Представляет собой объект главного справочника
 */
export class MainMisc {

   /**
    * Наименование справочника
    */
   public name: string;

   /**
    * id выбранного элемента справочника
    */
   public selectedItemId: number | null;

   /**
    * Строковый массив с наименованиями зависимых справочников
    */
   private readonly subAliases: string;

   /**
    * Менеджер зависимостей справочников
    */
   private miscDependenciesManager: MiscDependenciesManager;

   /**
    * Инпут со значением гланого поля
    */
   private readonly resultInput: HTMLInputElement;

   /**
    * Хранилище элементов зависимых справочников по значению главного поля
    */
   private subMiscItemsBySelectedId: Map<number, Map<string, MiscItem[]>> = new Map<number, Map<string, MiscItem[]>>();

   /**
    * Хранилище для получения значений зависимых справочников
    */
   public itemsGetters: Map<number, Promise<Map<string, MiscItem[]>>> = new Map<number, Promise<Map<string, MiscItem[]>>>();

   constructor(field: HTMLElement) {
      this.miscDependenciesManager = MiscDependenciesManager.getInstance();
      this.name = safeDataAttrGetter('name', field);
      this.resultInput = mQS(`[data-misc-result]`, field);
      this.subAliases = safeDataAttrGetter('subMiscAliases', field);
      htmlArrDecode(this.subAliases).forEach(subAlias => {
         this.miscDependenciesManager.addSubMiscKey(subAlias);
         Cache.slot(CacheSlots.MiscDependencies).set(subAlias, this);
      });


      this.handleResultObserver();
   }


   /**
    * Обрабатывает изменения главного поля
    */
   private handleResultObserver(): void {

      const observer: MutationObserver = new MutationObserver(mutations => {

         htmlArrDecode(this.subAliases).forEach((subAlias: string) => {
            this.miscDependenciesManager.clearSubMisc(subAlias);
         });

         if (mutations[0].oldValue !== this.resultInput.value) {


            this.selectedItemId = isNumeric(this.resultInput.value) ? parseInt(this.resultInput.value) : null;

            if (this.selectedItemId && !this.subMiscItemsBySelectedId.has(this.selectedItemId)) {
               this.itemsGetters.set(this.selectedItemId, this.requestMiscDependency());
            }

            htmlArrDecode(this.subAliases).forEach(subAlias => {
               this.miscDependenciesManager.addItemsToSubMisc(subAlias);
            });

         }

      });

      observer.observe(this.resultInput, {
         attributeFilter: ['value'],
         attributeOldValue: true
      });
   }

   /**
    * Получает значения зависимых справочников в зависимости от значения главного поля
    */
   public getItems(): Promise<Map<string, MiscItem[]>> {
      if (!this.selectedItemId) {
         return new Promise<Map<string, MiscItem[]>>(resolve => resolve(new Map()));
      } else {
         return safeMapGetter(this.itemsGetters, this.selectedItemId);
      }
   }

   /**
    * Запрашивает значения зависимых справочников
    */
   private requestMiscDependency(): Promise<Map<string, MiscItem[]>> {
      return new Promise<Map<string, MiscItem[]>>((resolve, reject) => {

         this.selectedItemId = isNumeric(this.resultInput.value) ? parseInt(this.resultInput.value) : null;

         Api.getMiscDependencies(this.resultInput.name, this.subAliases, this.selectedItemId!)
            .then((dependencies: MiscDependency[]) => {

               const subMiscItems: Map<string, MiscItem[]> = new Map<string, MiscItem[]>();

               dependencies.forEach(dependency => {
                  subMiscItems.set(dependency.subAlias, dependency.items)
               });

               this.subMiscItemsBySelectedId.set(this.selectedItemId!, subMiscItems);

               resolve(subMiscItems);

            })
            .catch(() => {
               reject();
               console.log('MiscDependencyGetter exc');
            });

      });


   }

}

