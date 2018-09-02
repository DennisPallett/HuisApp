import { Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { Observable } from "rxjs";
import { map } from 'rxjs/operators';
import { environment } from "../../environments/environment";
import { IMeterstand } from "./meterstand.model";

@Injectable()
export class MeterstandenService {

  private apiAction: string = "meterstanden";

  constructor(private httpClient: HttpClient) {
  }

  getMeterstanden(): Observable<IMeterstand[]> {
    return this.httpClient.get<IMeterstand[]>(environment.apiUrl + this.apiAction);
  }

  getMeterstand(opnameDatum: string): Observable<IMeterstand> {
    return this.httpClient.get<IMeterstand>(environment.apiUrl + this.apiAction + "/" + opnameDatum);
  }

  insertMeterstand(meterstand: IMeterstand): Observable<boolean> {
    return this.httpClient.post<boolean>(environment.apiUrl + this.apiAction, meterstand);
  }

  saveMeterstand(meterstand: IMeterstand): Observable<boolean> {
    return this.httpClient.post<boolean>(environment.apiUrl + this.apiAction + "/" + meterstand.opnameDatum, meterstand);
  }

  delete(meterstand: IMeterstand): Observable<boolean> {
    return this.httpClient.delete<boolean>(environment.apiUrl + this.apiAction + "/" + meterstand.opnameDatum, {});
  }

}
