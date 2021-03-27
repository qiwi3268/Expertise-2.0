import { mQS, safeDataAttrGetter } from '../lib/main';
import { SingleDependency } from './SingleDependenciesManager';
import { MultipleDependency } from './MultipleDependenciesManager';

/**
 * Типы зависимостей
 */
export enum Dependencies {
   SingleRequire = 'singleRequireDependencies',
   MultipleRequire = 'multipleRequireDependencies',
   SingledDisplay = 'singledDisplayDependencies',
   MultipleDisplay = 'multipleDisplayDependencies',
}

/**
 * Описывает конфигурацию с зависимостями
 */
export type FieldDependencies<T> = {
   [fieldName: string]: T
}

/**
 * Описывает зависимость элемента
 */
export type ElementDependencies = {
   [elementName: string]: boolean;
}

/**
 * Типы селекторов элементов, на которые действуют зависимости
 */
export enum DependentElementSelectors {
   Field = '[data-field]',
   Block = '[data-display-block]'
}

/**
 * Типы зависимых атрибутов
 */
export enum DependentAttributes {
   Required = 'data-required',
   Displayed = 'data-displayed'
}

/**
 * Представляет собой интерфейс для работы с менеджером зависимостей
 */
export interface DependenciesHandler {

   /**
    * Инициализирует главные поля на странице
    */
   initPageMainFields(): void;

   /**
    * Инициализирует главное поле
    *
    * @param field - элемент поля
    * @param scope - область действия зависимостей
    */
   initMainField(field: HTMLElement, scope: HTMLElement): void;

   /**
    * Инициализирует главные поля в области действия
    *
    * @param scope - область действия зависимостей
    */
   handleNewMainFieldsParentElement(scope: HTMLElement): void;
}

/**
 * Предназначен для управления зависимостями
 */
export abstract class DependenciesManager<T extends SingleDependency | MultipleDependency> implements DependenciesHandler {

   /**
    * Объект с конфигурацией зависимостей
    */
   protected dependencies: FieldDependencies<T>;

   /**
    * Селектор элементов, на которые действуют зависимости
    */
   protected elementSelector: DependentElementSelectors;

   /**
    * Зависимый атрибут
    */
   protected targetAttribute: DependentAttributes;

   /**
    * Имена главных полей
    */
   public mainFieldNames: string[];

   /**
    * Элементы главных полей
    */
   public mainFields: HTMLElement[] = [];

   public constructor(
      dependencies: FieldDependencies<T>,
      elementSelector: DependentElementSelectors,
      targetAttribute: DependentAttributes
   ) {
      this.elementSelector = elementSelector;
      this.targetAttribute = targetAttribute;
      this.dependencies = dependencies;

      this.mainFieldNames = Object.keys(dependencies);
      this.mainFields = this.getMainFieldsFromScope(document.documentElement);
   }

   /**
    * Получает главные поля из области действия
    *
    * @param scope - область действия зависимостей
    */
   protected getMainFieldsFromScope(scope: HTMLElement): HTMLElement[] {
      const fields: HTMLElement[] = [];

      this.mainFieldNames.forEach((name: string) => {

         const fieldElement: HTMLElement | null = scope.querySelector(`[data-field][data-name=${name}]`);
         if (fieldElement) {
            fields.push(fieldElement);
         }

      });

      return fields;
   }

   /**
    * Меняет состояние зависимых полей в области действия
    *
    * @param dependencies - объект с зависимыми элементами
    * @param scope - область действия зависимостей
    */
   protected setDependentElementStates(dependencies: ElementDependencies, scope: HTMLElement): void {
      Object.entries(dependencies).forEach(dependency => {
         const dependentElement: HTMLElement | null = scope.querySelector(`${this.elementSelector}[data-name="${dependency[0]}"]`);
         if (dependentElement) {
            dependentElement.setAttribute(this.targetAttribute, dependency[1].toString());
         }
      });
   }

   /**
    * Инициализирует главные поля на странице
    */
   public initPageMainFields(): void {
      this.mainFields.forEach(field => this.initMainField(field, document.documentElement));
   }
   /**
    * Инициализирует главное поле
    *
    * @param field - элемент поля
    * @param scope - область действия зависимостей
    */
   public initMainField(field: HTMLElement, scope: HTMLElement): void {

      const fieldName = safeDataAttrGetter('name', field);
      const resultInput: HTMLInputElement = mQS(`[data-field-result]`, field);

      this.setDependentElementStates(this.dependencies[fieldName]['default'], scope);

      const observer: MutationObserver = new MutationObserver(mutations => {
         if (mutations[0].oldValue !== resultInput.value) {
            this.handleMainFieldMutation(fieldName, resultInput.value, scope);
         }
      });

      observer.observe(resultInput, {
         attributeFilter: ['value'],
         attributeOldValue: true
      });
   }

   /**
    * Обрабатывает изменение главного поля
    *
    * @param fieldName - наименование поля
    * @param value - новое значение
    * @param scope - область действия зависимостей
    */
   protected abstract handleMainFieldMutation(fieldName: string, value: string, scope: HTMLElement): void;

   /**
    * Инициализирует главные поля в области действия
    *
    * @param scope - область действия зависимостей
    */
   public handleNewMainFieldsParentElement(scope: HTMLElement): void {
      const newFields: HTMLElement[] = this.getMainFieldsFromScope(scope);
      newFields.forEach(field => {
         this.initMainField(field, scope)
      });
      this.mainFields.push(...newFields);
   }

}

