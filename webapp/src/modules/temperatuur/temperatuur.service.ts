import { Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { Observable } from "rxjs";
import { environment } from "../../environments/environment";
import { IImportResult } from "./importresult.model";

@Injectable()
export class TemperatuurService {

  private apiAction: string = "temperatuur";

  constructor(private httpClient: HttpClient) {
  }

  importFiles(files: FileList): Observable<IImportResult> {
    const formData: FormData = new FormData();

    for (let i = 0; i < files.length; i++) {
      let file = files.item(i);
      formData.append("file" + i, file, file.name);
    }

    return this.httpClient.post<IImportResult>(environment.apiUrl + this.apiAction + "/import", formData);
  }

}
