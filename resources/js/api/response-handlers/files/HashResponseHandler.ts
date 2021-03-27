import { ApiResponseHandler } from '../ApiResponseHandler';


export type FileHashingResponse = {
   hash: string
}

export class HashResponseHandler extends ApiResponseHandler<FileHashingResponse> {

}

