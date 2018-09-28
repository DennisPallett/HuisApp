import { Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { Observable } from "rxjs";
import { environment } from "../../environments/environment";
import { IImportResult } from "./importresult.model";
import { ITemperatuurPerMaand } from "./temperatuurPerMaand.model";
import { ITemperatuurPerDag } from "./temperatuurPerDag.model";

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

  getTemperatuurPerMaand(): Observable<ITemperatuurPerMaand[]> {
    return this.httpClient.get<ITemperatuurPerMaand[]>(environment.apiUrl + this.apiAction + "/per-maand");
  }

  getTemperatuurPerDag(): Observable<ITemperatuurPerDag[]> {
    return this.httpClient.get<ITemperatuurPerDag[]>(environment.apiUrl + this.apiAction + "/per-dag");
  }

}
