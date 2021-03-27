import { htmlArrEncode, mQS, safeDataAttrGetter } from '../../lib/main';

/**
 * Представляет собой блок с чекбоксами
 */
export class RadioBlock {

   /**
    * Элемент поля
    */
   protected readonly field: HTMLElement;

   /**
    * Флаг обязательности поля
    */
   protected readonly required: boolean;

   /**
    * Флаг множественного выбора
    */
   protected readonly multiple: boolean;

   /**
    * Элемент со значениями
    */
   protected container: HTMLElement;

   /**
    * Инпут с выбранными значениями
    */
   protected resultInput: HTMLInputElement;

   constructor(field: HTMLElement) {
      this.field = field;
      this.container = mQS('[data-radio-body]', this.field);

      this.required = safeDataAttrGetter('required', this.field) === 'true';
      this.multiple = safeDataAttrGetter('multiple', this.field) === 'true';

      this.resultInput = mQS('[data-field-result]', this.field);

      this.handleItems();
   }

   /**
    * Инициализирует чекбокс элементы блока
    */
   protected handleItems(): void {
      const items: NodeListOf<HTMLElement> = this.container.querySelectorAll('[data-radio-item]');
      items.forEach(item => this.initItem(item));
   }

   /**
    * Инициализирует чекбокс
    *
    * @param item - элемент чекбокса
    */
   protected initItem(item: HTMLElement): void {
      item.addEventListener('click', () => this.selectItem(item));
   }

   /**
    * Выбирает элемент
    *
    * @param item - выбранный элемент
    */
   protected selectItem(item: HTMLElement): void {
      this.changeState(item);
      this.resultInput.value = this.getRadioResult();
   }

   /**
    * Меняет состояние блока
    *
    * @param item - выбранный элемент
    */
   protected changeState(item: HTMLElement): void {

      if (item.dataset.selected === 'true') {
         removeCheckbox(item);
      } else if (this.multiple) {
         setCheckbox(item);
      } else {
         this.changeSelectedItem(item);
      }

   }

   /**
    * Получает выбранные элементы
    */
   protected getRadioResult(): string {
      const result: number[] = [];

      const selectedItems: NodeListOf<HTMLElement> = this.container.querySelectorAll('[data-radio-item][data-selected="true"]');
      selectedItems.forEach(item => result.push(parseInt(safeDataAttrGetter('id', item))));

      if (result.length > 0) {
         return this.multiple ? htmlArrEncode(result) : result[0].toString();
      } else {
         return '';
      }
   }

   /**
    * Меняет выбранный элемент
    *
    * @param item - новый выбранный элемент
    */
   private changeSelectedItem(item: HTMLElement) {
      const selectedItem: HTMLElement | null = this.container.querySelector('[data-radio-item][data-selected="true"]');

      if (!selectedItem) {
         setCheckbox(item);
      } else {
         removeCheckbox(selectedItem);
         setCheckbox(item);
      }
   }

}

/**
 * Снимает чекбокс
 *
 * @param radioItem - элемент чекбокса
 */
export function removeCheckbox(radioItem: HTMLElement): void {
   radioItem.dataset.selected = 'false';
   const icon: HTMLElement = mQS('[data-radio-icon]', radioItem);
   icon.classList.remove('fa-check-square');
   icon.classList.add('fa-square');
}

/**
 * Устанавливает чекбокс
 *
 * @param radioItem - элемент чекбокса
 */
export function setCheckbox(radioItem: HTMLElement): void {
   radioItem.dataset.selected = 'true';
   const icon: HTMLElement = mQS('[data-radio-icon]', radioItem);
   icon.classList.remove('fa-square');
   icon.classList.add('fa-check-square');
}

/**
 * Меняет переключатель
 *
 * @param switchItem - элемент переключателя
 */
export function toggleSwitch(switchItem: HTMLElement): void {
   switchItem.dataset.selected === 'true' ? offSwitch(switchItem) : onSwitch(switchItem);
}

/**
 * Выключает переключатель
 *
 * @param switchItem - элемент переключателя
 */
export function offSwitch(switchItem: HTMLElement): void {
   const icon: HTMLElement = mQS('[data-switch-icon]', switchItem);
   switchItem.setAttribute('data-selected', 'false');
   icon.classList.remove('fa-toggle-on');
   icon.classList.add('fa-toggle-off');
}

/**
 * Включает переключатель
 *
 * @param switchItem - элемент переключателя
 */
export function onSwitch(switchItem: HTMLElement): void {
   const icon: HTMLElement = mQS('[data-switch-icon]', switchItem);
   switchItem.setAttribute('data-selected', 'true');
   icon.classList.remove('fa-toggle-off');
   icon.classList.add('fa-toggle-on');
}
