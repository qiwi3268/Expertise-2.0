import { mGetByID } from '../lib/main';
import { SimpleRadioBlocksManager } from '../forms/radio/SimpleRadioBlocksManager';
import { ErrorModal } from '../modals/ErrorModal';

document.addEventListener('DOMContentLoaded', () => {
   SimpleRadioBlocksManager.getInstance().initPageRadioBlocks();

   const form: HTMLFormElement = mGetByID('loginForm');
   const submit: HTMLElement = mGetByID('loginSubmit');
   submit.addEventListener('click', () => form.submit());

   const loginError: HTMLElement = mGetByID('loginError');
   const errorMessage: string = loginError.textContent !== null ? loginError.textContent.trim() : '';
   if (errorMessage !== '') {
      ErrorModal.open('Ошибка при авторизации', errorMessage);
   }

});
