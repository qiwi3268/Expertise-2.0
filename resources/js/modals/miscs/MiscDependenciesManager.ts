import { safeMapGetter } from '../../lib/main';
import { MainMisc } from './MainMisc';
import { SubMisc } from './modals/DependentMiscModal';
import { MiscItem } from './modals/MiscModal';
import { Cache, CacheSlots } from '../../lib/Cache';

/**
 * Описывает зависимость справочника
 */
export type MiscDependency = {
   items: MiscItem[],
   subAlias: string
}

/**
 * Представляет собой класс для работы с зависимостями справочников
 */
export class MiscDependenciesManager {

   private static instance: MiscDependenciesManager;

   /**
    * Зависимые справочники
    */
   public subMiscs: Map<string, SubMisc | null> = new Map<string, SubMisc | null>();

   public static getInstance(): MiscDependenciesManager {

      if (!MiscDependenciesManager.instance) {
         MiscDependenciesManager.instance = new MiscDependenciesManager();
      }

      return MiscDependenciesManager.instance;
   }

   /**
    * Инициализирует главные справочники на странице
    */
   public initPageMainMiscs(): void {
      const mainMiscFields: NodeListOf<HTMLElement> = document.querySelectorAll('[data-sub-misc-aliases]');
      mainMiscFields.forEach(field => new MainMisc(field));
   }

   /**
    * Добавляет ключ зависимого справочника
    *
    * @param miscName - наименование зависимого справочника
    */
   public addSubMiscKey(miscName: string): void {
      this.subMiscs.set(miscName, null);
   }

   /**
    * Добавляет объект зависимого справочника в хранилище
    *
    * @param misc - зависимый справочник
    */
   public addSubMiscValue(misc: SubMisc): void {
      this.subMiscs.set(misc.getName(), misc);
   }

   /**
    * Добавляет элементы в зависимый справочник
    *
    * @param subMiscName - наименование зависимого справочника
    */
   public addItemsToSubMisc(subMiscName: string): void {
      const subMisc: SubMisc | null = safeMapGetter(this.subMiscs, subMiscName);
      if (subMisc) {
         subMisc.getItems();
      }
   }

   /**
    * Удаляет значения из зависимого справочника
    *
    * @param subMiscName - наименование зависимого справочника
    */
   public clearSubMisc(subMiscName: string): void {
      const subMisc: SubMisc | null = safeMapGetter(this.subMiscs, subMiscName);
      if (subMisc) {
         subMisc.removeItems();
      }
   }

   /**
    * Получает значения для зависимого справочника
    *
    * @param subMiscName - наименование зависимого справочника
    */
   public getItemsBySubMiscName(subMiscName: string): Promise<MiscItem[]> {

      return new Promise<MiscItem[]>((resolve, reject) => {

         if (Cache.slot(CacheSlots.MiscDependencies).has(subMiscName)) {

            const mainMisc: MainMisc = Cache.slot(CacheSlots.MiscDependencies).get<MainMisc>(subMiscName);

            mainMisc.getItems().then(response => {
               if (response.has(subMiscName)) {
                  resolve(safeMapGetter(response, subMiscName));
               } else {
                  reject();
               }
            })
               .catch(() => {
                  resolve([]);
               })
         }

      });

   }

}


