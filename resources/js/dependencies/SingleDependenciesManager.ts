import { DependenciesManager, ElementDependencies } from './DependenciesManager';
import { LogicError } from '../lib/LogicError';

/**
 * Описывает одиночные зависимости
 */
export type SingleDependency = {
   [selectedItemId in number | 'default']: ElementDependencies
};

export class SingleDependenciesManager extends DependenciesManager<SingleDependency> {

   /**
    * Обрабатывает изменение главного поля
    *
    * @param fieldName - наименование поля
    * @param value - новое значение
    * @param scope - область действия зависимостей
    */
   protected handleMainFieldMutation(fieldName: string, value: string, scope: HTMLElement): void {

      let selectedValue: number | 'default' = value !== '' ? parseInt(value) : 'default';

      if (!this.dependencies[fieldName].hasOwnProperty(selectedValue)) {
         new LogicError(`Отсутствует зависимость блоков по значению: "${selectedValue}" в объекте зависимостей: ${fieldName}`);
         selectedValue = 'default';
      }

      this.setDependentElementStates(this.dependencies[fieldName][selectedValue], scope);
   }

}
