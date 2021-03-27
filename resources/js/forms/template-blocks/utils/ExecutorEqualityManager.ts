import { TemplateBlock } from '../blocks/TemplateBlock';

export class ExecutorEqualityManager {

   private executors: TemplateBlock[] = [];

   private static instance: ExecutorEqualityManager;

   private constructor() {
   }

   public static getInstance(): ExecutorEqualityManager {

      if (!ExecutorEqualityManager.instance) {
         ExecutorEqualityManager.instance = new ExecutorEqualityManager();
      }

      return ExecutorEqualityManager.instance;
   }

   public saveExecutor(executor: TemplateBlock): void {
      this.executors.push(executor);

      document.querySelectorAll<HTMLElement>('[data-field][data-name="equality"]')
         .forEach(field => field.dataset.displayed = 'true');
   }

   public getSavedExecutors(): TemplateBlock[] {
      return this.executors;
   }


}
