import { DependenciesManager, ElementDependencies } from './DependenciesManager';
import { LogicError } from '../lib/LogicError';
import { htmlArrDecode } from '../lib/main';

/**
 * Описывает множественные зависимости
 */
export type MultipleDependency = {
   default: ElementDependencies,
   rules: {
      [validationRule: string]: ElementDependencies
   }
}

/**
 * Правила множественных зависимостей
 */
enum MultipleDependenciesRules {
   INCLUDE_ANY = 'INCLUDE_ANY:',
   EXCLUDE_ALL = 'EXCLUDE_ALL:'
}

/**
 * Предназначен для обработки множественных зависимостей
 */
export class MultipleDependenciesManager extends DependenciesManager<MultipleDependency> {

   /**
    * Обрабатывает изменение главного поля
    *
    * @param fieldName - наименование поля
    * @param value - новое значение
    * @param scope - область действия зависимостей
    */
   protected handleMainFieldMutation(fieldName: string, value: string, scope: HTMLElement): void {

      const selectedValue: string[] | 'default' = value ? htmlArrDecode(value) : 'default';

      if (selectedValue === 'default') {

         this.setDependentElementStates(this.dependencies[fieldName]['default'], scope);

      } else {

         const dependenciesByRules = this.dependencies[fieldName].rules;
         const acceptedRules: string[] = Object.keys(dependenciesByRules).filter(dependencies => {
            const validate: Function = this.getValidationCallbackByDependencyKey(dependencies);
            return validate(selectedValue);
         });

         acceptedRules.forEach((rule: string) => this.setDependentElementStates(dependenciesByRules[rule], scope));
      }
   }

   /**
    * Возвращает колбэк валидации выбранного значения главного поля по правилу зависимости
    *
    * @param dependencyKey - правило зависимости
    */
   protected getValidationCallbackByDependencyKey(dependencyKey: string): Function {

      const acceptableValues: string[] = this.getAcceptableFieldValuesFromDependencyKey(dependencyKey);

      if (dependencyKey.includes(MultipleDependenciesRules.INCLUDE_ANY)) {

         return (fieldValue: string[]) => fieldValue.some(value => acceptableValues.includes(value));

      } else if (dependencyKey.includes(MultipleDependenciesRules.EXCLUDE_ALL)) {

         return (fieldValue: string[]) => !fieldValue.some(value => acceptableValues.includes(value));

      } else {
         new LogicError(`Не определено правило валидации множественной зависимости по ключу: ${dependencyKey}`);
         return () => false;
      }
   }

   /**
    * Возвращает значения главного поля, к которым применяется правило зависимости
    *
    * @param dependencyKey - правило зависимости
    */
   private getAcceptableFieldValuesFromDependencyKey(dependencyKey: string): string[] {
      return dependencyKey.substring(dependencyKey.indexOf(':') + 1).split('#');
   }

}
