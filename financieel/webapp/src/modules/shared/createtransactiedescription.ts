import { Pipe } from "@angular/core";
import { ITransactie } from "./transactie.model";

@Pipe({
  name: 'CreateTransactieDescription'
})

export class CreateTransactieDescriptionPipe {
  transform(value: ITransactie): string {
    if (value.is_card_payment == false && value.remittance_info != null)
      return value.remittance_info

    if (value.is_card_payment && value.is_cash_withdrawal)
      return "Geld pinnen";

    if (value.is_card_payment) {
      return "Pinbetaling: " + value.description;
    }

    return value.other_party_name;
  }
}
