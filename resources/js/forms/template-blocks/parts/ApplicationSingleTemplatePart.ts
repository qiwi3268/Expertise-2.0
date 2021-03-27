import { SingleTemplatePart } from './SingleTemplatePart';
import { ApplicationFormCardsManager } from '../../cards/ApplicationFormCardsManager';

export class ApplicationSingleTemplatePart extends SingleTemplatePart {

   protected save(): void {
      super.save();
      ApplicationFormCardsManager.getInstance().shrinkParentCard(this.templateBlock.getElement());
      this.templateBlock.getElement().scrollIntoView({block: 'start', behavior: 'smooth'});

   }

}
