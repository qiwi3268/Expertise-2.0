import { ApiResponseHandler } from '../ApiResponseHandler';
import { StringForm } from '../../../forms/blocks/FormBlock';

export type Dadata = {
   data: StringForm
   found: boolean
}

export class OrganizationByOrgInnResponseHandler extends ApiResponseHandler<Dadata> {

}
