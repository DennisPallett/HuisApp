import { Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { Observable } from "rxjs";
import { map } from 'rxjs/operators';
import { environment } from "../../environments/environment";
import { IMeterstand } from "./meterstand.model";
import { IVerbruikPerMaand } from "./verbruikPerMaand.model";

@Injectable()
export class VerbruikService {

  private apiAction: string = "verbruik";

  constructor(private httpClient: HttpClient) {
  }

  getPerMaand(): Observable<IVerbruikPerMaand[]> {
    return this.httpClient.get<IVerbruikPerMaand[]>(environment.apiUrl + this.apiAction + "/per-maand");
  }
  
}
