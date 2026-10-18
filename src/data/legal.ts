export const legal = {
  version: "2026-10-18-draft",
  brand: "IT LENS",
  operatorName: "[KITÖLTENDŐ: szolgáltató/adatkezelő teljes jogi neve]",
  registeredOffice: "[KITÖLTENDŐ: székhely / egyéni vállalkozó esetén nyilvántartott cím]",
  registrationAuthority: "[KITÖLTENDŐ: nyilvántartó bíróság vagy hatóság]",
  registrationNumber: "[KITÖLTENDŐ: cégjegyzékszám / EV nyilvántartási szám]",
  taxNumber: "[KITÖLTENDŐ: adószám]",
  email: "info@it-lens.hu",
  phone: "[KITÖLTENDŐ, ha rendszeresen használt kapcsolattartási csatorna]",
  hostingProvider: {
    name: "Sybell Informatika Kft.",
    address: "1138 Budapest, Tomori utca 34. 2. emelet",
    email: "info@sybell.hu",
    phone: "+36 1 707 67 27",
    website: "https://sybell.hu"
  }
};

export const legalIdentityComplete =
  !legal.operatorName.includes("[KITÖLTENDŐ") &&
  !legal.registeredOffice.includes("[KITÖLTENDŐ") &&
  !legal.registrationAuthority.includes("[KITÖLTENDŐ") &&
  !legal.registrationNumber.includes("[KITÖLTENDŐ") &&
  !legal.taxNumber.includes("[KITÖLTENDŐ");
