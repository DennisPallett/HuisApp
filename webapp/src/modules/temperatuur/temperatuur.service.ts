import { Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { Observable } from "rxjs";
import { environment } from "../../environments/environment";
import { IImportResult } from "./importresult.model";
import { ITemperatuurPerPeriode } from "./temperatuurPerPeriode.model";

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

  getTemperatuurPerMaand(): Observable<ITemperatuurPerPeriode[]> {
    return this.httpClient.get<ITemperatuurPerPeriode[]>(environment.apiUrl + this.apiAction + "/per-maand");
  }

  getTemperatuurPerDag(year?: number, month?: number): Observable<ITemperatuurPerPeriode[]> {
    var strMonth = (month != null) ? month.toString() : null;
    var strYear = (year != null) ? year.toString() : null;

    return this.httpClient.get<ITemperatuurPerPeriode[]>(environment.apiUrl + this.apiAction + "/per-dag", {
      params:
      {
        month: strMonth,
        year: strYear
      }
    });
  }

  getTemperatuurPerUur(year: number, month: number): Observable<ITemperatuurPerPeriode[]> {
    return this.httpClient.get<ITemperatuurPerPeriode[]>(environment.apiUrl + this.apiAction + "/per-uur", {
      params:
      {
        month: month.toString(),
        year: year.toString()
      }
    });
  }

}
