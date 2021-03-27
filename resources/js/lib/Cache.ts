import { LogicError } from './LogicError';
import { safeMapSetter, safeWeakMapSetter } from './main';

/**
 * Типы сущностей, которые могут быть помещены в хранилище
 */
export enum CacheSlots {
   MiscDependencies = 'MiscDependencies',
   FieldDependencies = 'FieldDependencies',
   TemplateBlocks = 'TemplateBlocks',
   PageManagers = 'PageManagers'
}

/**
 * Типы менеджеров элементов страницы
 */
export enum PageManagers {
   MiscModal = 'MiscModal',
}

/**
 * Представляет собой класс для работы с глобальным хранилищем
 */
export class Cache {

   private static instance: Cache = new Cache();

   /**
    * Хранилище разбитое по типам сущностей
    */
   private slots: Map<CacheSlots, Map<unknown, unknown>> = new Map<CacheSlots, Map<unknown, unknown>>();

   /**
    * Выбранное хранилище по типу сущности
    */
   private currentSlot: Map<unknown, unknown> | null = null;

   private constructor() {
   }

   /**
    * Создает часть хранилища
    *
    * @param slotName - тип сущности
    * @return класс для работы с хранилищем
    */
   public static createSlot(slotName: CacheSlots): Cache {

      try {

         if (Cache.instance.slots.has(slotName)) {
            throw new LogicError(`Ошибка при создании слота: слот по ключу ${slotName} уже создан`);
         }

         Cache.instance.slots.set(slotName, new Map<unknown, unknown>());

      } catch (exc) {
      }

      return Cache.instance;
   }

   /**
    * Устанавливает выбранное хранилище по типу сущности
    *
    * @param slotName - тип сущности
    * @return класс для работы с хранилищем
    */
   public static slot(slotName: CacheSlots): Cache {
      Cache.instance.currentSlot = Cache.instance.slots.get(slotName)!;

      if (!Cache.instance.currentSlot) {
         new LogicError(`Ошибка при получении кэша: слот по ключу ${slotName} не создан`);
      }

      return Cache.instance;
   }

   /**
    * Устанавливает в выбранное хранилище значение по ключу
    *
    * @param key - ключ для установки значения
    * @param data - значение
    */
   public set(key: unknown, data: unknown): void {

      try {

         if (!this.currentSlot) {
            throw new LogicError(`Не определен слот для установки значения`);
         }

         if (this.currentSlot.has(key)) {
            throw new LogicError(`Ошибка при установке значения слота: значение слота по ключу: ${key} уже установлено`);
         }

         safeMapSetter(this.currentSlot, key, data);

      } catch (exc) {

      } finally {
         this.currentSlot = null;
      }

   }

   /**
    * Определяет, есть ли в выбранном хранилище значение по ключу
    *
    * @param key - ключ для проверки
    */
   public has(key: unknown): boolean | undefined {

      try {

         if (!this.currentSlot) {
            throw new LogicError(`Не определен слот для проверки значения`);
         }

         return this.currentSlot.has(key);

      } catch (exc) {

      } finally {
         this.currentSlot = null;
      }

   }

   /**
    * Получает из выбранного хранилища значение по ключу
    *
    * param key - ключ для получения
    */
   public get<T = unknown>(key: unknown): T {

      if (!this.currentSlot) {
         this.currentSlot = null;
         throw new LogicError(`Не определен слот для получения значения`);
      }

      if (!this.currentSlot.has(key)) {
         new LogicError(`Ошибка при получении значения слота: отсутствует значение по ключу ${key}`);
      }

      const value: T = this.currentSlot.get(key) as T;

      this.currentSlot = null;

      return value;
   }

   /**
    * Обновляет в выбранном хранилище значение по ключу
    *
    * @param key - ключ для обновление
    * @param callback - колбэк обновления значения
    */
   public update(key: unknown, callback: Function): void {

      try {

         if (!this.currentSlot) {
            throw new LogicError(`Не определен слот для обновления значения`);
         }

         if (!this.currentSlot.has(key)) {
            throw new LogicError(`Ошибка при получении значения слота: отсутствует значение по ключу ${key}`);
         }

         const value = this.currentSlot.get(key);

         callback(value);

      } catch (exc) {

      } finally {
         this.currentSlot = null;
      }

   }

   /**
    * Получает выбранное хранилище
    */
   public getValue<K = unknown, V = unknown>(): Map<K, V> {

      if (!this.currentSlot) {
         throw new LogicError(`Не определен слот для получения хранилища`);
      }

      const value: Map<K, V> = this.currentSlot as Map<K, V>;

      this.currentSlot = null;

      return value;
   }

}






























