import { SingleMiscModal } from './SingleMiscModal';
import { mClosest, safeDataAttrGetter } from '../../../lib/main';
import { ExecutorEqualityManager } from '../../../forms/template-blocks/utils/ExecutorEqualityManager';
import { ApplicationTemplateBlocksManager } from '../../../forms/template-blocks/ApplicationTemplateBlocksManager';
import { TemplateBlock } from '../../../forms/template-blocks/blocks/TemplateBlock';

export class ExecutorEqualityMiscModal extends SingleMiscModal {

   public executorType: string;
   public executor: TemplateBlock;

   public constructor(select: HTMLElement) {
      super(select)
      const mutableBlock: HTMLElement = mClosest('[data-mutable-block]', this.field);

      this.executorType = safeDataAttrGetter('type', mutableBlock);
      this.executor = ApplicationTemplateBlocksManager.getInstance().getSingleExecutorByType(this.executorType);

   }

   public open(): void {
      this.filterItemsBySavedExecutors(ExecutorEqualityManager.getInstance().getSavedExecutors());
   }

   protected filterItemsBySavedExecutors(executors: TemplateBlock[]): void {
      this.container.textContent = '';

   }

   protected selectItem(event: MouseEvent): void {
      super.selectItem(event);
   }

}
