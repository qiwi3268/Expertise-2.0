require('../bootstrap');
import { LogicError } from './LogicError';

/**
 * Объект для передачи данных из php в js
 */
export const jst: JSTransfer = (window as any).jst;

/**
 * Описывает данные, которые могут быть переданы из php в js
 */
export type JSTransfer = {
   targetDocumentId: string
}

/**
 * Получает первый элемент по указанному селектору
 * внутри переданного элемента, вызывает алерт с ошибкой,
 * если элемент не найден
 *
 * @param selector - селектор для поиска
 * @param element - элемент, внутри которого ищется совпадение
 * @return ближайший внутренний элемент
 */
export function mQS<T extends Element>(selector: string, element: Element): T {
   const result: T = element.querySelector(selector) as T;
   if (result === null) {
      new LogicError(`Не найден элемент по селектору: ${selector}`);
   }
   return result;
}

/**
 * Получает NodeList со всеми элементами по указанному селектору
 * внутри переданного элемента (document, если не передан параметр),
 * вызывает алерт с ошибкой, если элементы не найден
 *
 * @param selector - селектор для поиска
 * @param element - элемент, внутри которого ищется совпадение
 * @return NodeList с найденными элементами
 */
export function mQSA<T extends Element>(selector: string, element: Element): T[] {
   const result: T[] = Array.from(element.querySelectorAll<T>(selector));
   if (result.length === 0) new LogicError(`Не найдены элементы по селектору: ${selector}`);
   return result;
}

/**
 * Возвращает ближайший родительский элемент, который соответствует
 * заданному селектору или вызывает алерт с ошибкой, если элемент не найден
 *
 * @param selector - селектор для поиска
 * @param element - элемент, для которого ищется родитель
 * @return ближайший родительский элемент
 */
export function mClosest<T extends Element>(selector: string, element: Element): T {
   const result: T = element.closest(selector) as T;
   if (result === null) new LogicError(`Не найден родительский элемент по селектору: ${selector}`);
   return result;
}

/**
 * Возвращает элемент страницы по указанному id,
 * вызывает алерт с ошибкой, если элемент не найден
 *
 * @param id - идентификатор элемента
 * @return элемент с указанным id
 */
export function mGetByID<T extends HTMLElement>(id: string): T {
   const result: T = document.getElementById(id) as T;
   if (result === null) new LogicError(`Не найден элемент по id: ${id}`);
   return result;
}

/**
 * Получает значение в переданной Map по заданному ключу или
 * вызывает алерт с ошибкой, если значение не найдено
 *
 * @param map - Map, в которой ищется элемент
 * @param key - ключ для поиска
 */
export function safeMapGetter<T, V>(map: Map<T, V>, key: T): V {
   const element: V = map.get(key) as V;
   if (element === undefined) {
      new LogicError(`Не найдет элемент в Map по ключу: ${key}`);
      console.error(map);
   }
   return element;
}

/**
 * Устанавливает значение в Map, если по данному ключу
 * не записано значение, иначе выводит алерт с ошибкой
 *
 * @param map - Map, в которой ищется элемент
 * @param key - ключ для поиска
 * @param value - устанавливаемое значение
 */
export function safeMapSetter<T, V>(map: Map<T, V>, key: T, value: V): void {

   if (map.has(key)) {
      new LogicError(`Элемент Map по ключу: ${key} уже установлен`);
      console.error(map);
   } else {
      map.set(key, value);
   }

}

/**
 * Получает значение в переданной WeakMap по заданному ключу или
 * вызывает алерт с ошибкой, если значение не найдено
 *
 * @param map - Map, в которой ищется элемент
 * @param key - ключ для поиска
 */
export function safeWeakMapGetter<T extends object, V>(map: WeakMap<T, V>, key: T): V {
   const element: V = map.get(key) as V;
   if (element === undefined) {
      new LogicError(`Не найдет элемент в WeakMap по ключу: ${key}`);
      console.error(map);
   }
   return element;
}

/**
 * Устанавливает значение в WeakMap, если по данному ключу
 * не записано значение, иначе выводит алерт с ошибкой
 *
 * @param map - Map, в которой ищется элемент
 * @param key - ключ для поиска
 * @param value - устанавливаемое значение
 */
export function safeWeakMapSetter<T extends object, V>(map: WeakMap<T, V>, key: T, value: V): void {

   if (map.has(key)) {
      new LogicError(`Элемент Map по ключу: ${key} уже установлен`);
      console.error(map);
   } else {
      map.set(key, value);
   }

}

/**
 * Получает указанный дата атрибут у переданного элемента
 * или вызывает алерт с ошибкой, если атрибут отсутствует
 *
 * @param attr - получаемый дата атрибут
 * @param elem - элемент для получения
 */
export function safeDataAttrGetter(attr: string, elem: HTMLElement): string {
   const result: string = elem.dataset[attr] as string;
   if (result === undefined) new LogicError(`Не найдет атрибут: ${attr} у элемента: ${elem.classList}`);
   return result;
}

/**
 * Отключает стандартные действия переноса в браузере
 */
export function clearDefaultDropEvents(): void {
   const events = ['dragenter', 'dragover', 'dragleave', 'drop'];
   events.forEach(event_name => {
      document.addEventListener(event_name, event => {
         event.preventDefault();
         event.stopPropagation();
      });
   });
}

/**
 * Создает объекта даты из строки
 *
 * @param dateString - дата в виде строки
 * @return объект даты
 */
export function getDateFromString(dateString: string): Date {
   const dateParts: string[] = dateString.split('.');
   return new Date(
      parseInt(dateParts[2]),
      parseInt(dateParts[1]) - 1,
      parseInt(dateParts[0])
   );
}

/**
 * Разбирает строку JSON в объект
 * <br/>Выводит алерт с ошибкой,
 * если разбираемая строка не является правильным JSON
 *
 * @param json - строка JSON
 */
export function safeJSONParse<T = unknown>(json: string): T {
   let obj = {};
   try {
      obj = JSON.parse(json);
   } catch (exc) {
      new LogicError(`Ошибка при разборе строки JSON: "${json}"`);
   }

   return obj as T;
}

/**
 * Определяет существует ли элемент на странице
 *
 * @param element - элемент для проверки
 */
export function isDisplayedElement(element: HTMLElement): boolean {
   return !element.closest('[data-display-block][data-displayed="false"]');
}

/**
 * Определяет, является ли объект частью шаблона
 *
 * @param element - элемент для проверки
 */
export function isNotTemplate(element: HTMLElement): boolean {
   return !element.closest('[data-template="true"]');
}

/**
 * Получает высоту элемент на странице в пикселях
 *
 * @param element - элемент для получения высоты
 */
export function getElementFullHeight(element: HTMLElement) {
   const style = window.getComputedStyle(element);
   return element.scrollHeight + parseInt(style.marginTop) + parseInt(style.marginBottom);
}

/**
 * Определяет, является ли строка числом
 *
 * @param str - строка для проверки
 */
export function isNumeric(str: string): boolean {
   return /^\d+$/.test(str);
}

/**
 * Добавляет действия изменения значения поля
 *
 * @param field - поля, для которого отслеживается изменение
 * @param handler - действие при изменении
 */
export function addFieldMutationHandler(field: HTMLElement, handler: Function): void {
   const resultInput: HTMLInputElement = mQS('[data-field-result]', field);

   if (field.dataset.type !== 'input') {

      const observer: MutationObserver = new MutationObserver((mutations: MutationRecord[]) => {

         if (mutations[0].oldValue !== resultInput.value) {
            handler(resultInput.value);
         }

      });

      observer.observe(resultInput, {
         attributeFilter: ['value'],
         attributeOldValue: true
      });

   } else {
      resultInput.addEventListener('change', () => handler(resultInput.value.trim()));
   }

}

/**
 * Добавляет действие изменения атрибута элемента
 *
 * @param element - элемент, у которого отслеживается атрибут
 * @param attribute - отслеживаемый атрибут
 * @param handler - действие при изменении атрибута
 */
export function addDataAttrMutationHandler(element: HTMLElement, attribute: string, handler: Function): void {
   const observer: MutationObserver = new MutationObserver((mutations: MutationRecord[]) => {
      const attrValue = element.dataset[attribute];

      if (mutations[0].oldValue !== attrValue) {
         handler(attrValue);
      }
   });

   observer.observe(element, {
      attributeFilter: [`data-${attribute}`],
      attributeOldValue: true
   });
}

/**
 * Получает регулярное выражение из строки
 *
 * @param regStr - строка с регулярным выражением
 */
export function getRegExpFromString(regStr: string): RegExp {
   const regBody: string = regStr.substr(1, regStr.lastIndexOf('$'));
   const params: string = regStr.substr(regStr.lastIndexOf('/') + 1);

   return params.length > 0 ? new RegExp(regBody, params) : new RegExp(regBody)
}

/**
 * Координаты элемента
 */
export type Coordinates = {
   top: number,
   left: number
}

/**
 * Получает координаты элемента относительно документа
 *
 * @param elem - элемент, координаты которого нужно получить
 * @return объект с координатами
 */
export function getElementCoords(elem: HTMLElement): Coordinates {

   const box: DOMRect = elem.getBoundingClientRect();

   return {
      top: box.top + pageYOffset,
      left: box.left + pageXOffset
   };
}

/**
 * Преобразует массив в html массив в виде строки
 *
 * @param values - массив для преобазования
 * @return {string}
 */
export function htmlArrEncode<T>(values: Array<T>): string {
   return values.join('#|$');
}

/**
 * html массив из дата атрибута в виде строки в массив строк
 *
 * @param value - html массив
 */
export function htmlArrDecode(value: string): string[] {
   const arr: string[] = value.split('#|$');

   // Несколько символов разделителей следуют друг за другом
   if (arr.find((elem: string) => elem === '')) {
      new LogicError('Пустые элементы в html массиве');
   }

   return arr;
}

