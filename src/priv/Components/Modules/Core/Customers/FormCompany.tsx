import React, { Component } from "react";
import { deepObjectMerge } from "adios/Helper";
import Form, { FormProps, FormState } from "adios/Form";
import InputVarchar from "adios/Inputs/Varchar";
import InputTags2 from "adios/Inputs/Tags2";
import InputTable from "adios/Inputs/Table";
import FormInput from "adios/FormInput";
import { Column } from "primereact/column";

interface FormCompanyProps extends FormProps {}

interface FormCompanyState extends FormState {}

export default class FormCompany<P, S> extends Form<
  FormCompanyProps,
  FormCompanyState
> {
  static defaultProps: any = {
    model: "CeremonyCrmApp/Modules/Core/Customers/Models/Company",
  };

  props: FormCompanyProps;
  state: FormCompanyState;

  constructor(props: FormCompanyProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormCompanyProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  normalizeRecord(record) {
    if (record.PERSONS) record.PERSONS.map((item: any, key: number) => {
        record.PERSONS[key].id_company = { _useMasterRecordId_: true };
    });
    if (record.ACTIVITIES) record.ACTIVITIES.map((item: any, key: number) => {
        record.ACTIVITIES[key].id_company = { _useMasterRecordId_: true };
    });
    if (record.BUSINESS_ACCOUNT) {
      record.BUSINESS_ACCOUNT.id_company = { _useMasterRecordId_: true };
    }

    return record;
  }

  renderTitle(): JSX.Element {
    return (
      <>
        <h2>{this.state.record.name ?? "[no-name]"}</h2>
      </>
    );
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    return (
      <>
        <div className="grid grid-cols-2 gap-1">
          <div>
            <div className="card mt-4">
              <div className="card-header">Company Information</div>
              <div className="card-body">
                {this.inputWrapper("name")}
                {this.inputWrapper("street")}
                {this.inputWrapper("city")}
                {this.inputWrapper("country")}
                {this.inputWrapper("postal_code")}
                {this.inputWrapper("vat_id")}
                {this.inputWrapper("tax_id")}
                {this.inputWrapper("company_id")}
              </div>
            </div>

            <div className="card mt-4">
              <div className="card-header">Contacts</div>
              <div className="card-body">
                <InputTable
                  {...this.getDefaultInputProps()}
                  model="CeremonyCrmApp/Modules/Core/Customers/Models/Person"
                  value={R.PERSONS}
                  onChange={(value: any) => {
                    this.updateRecord({ PERSONS: value });
                  }}
                  columns={{
                    first_name: { type: "varchar", title: "First name" },
                    last_name: { type: "varchar", title: "Last name" },
                  }}
                ></InputTable>
                {this.state.isInlineEditing ? (
                  <a
                    role="button"
                    onClick={() => {
                      if (!R.PERSONS) R.PERSONS = [];
                      R.PERSONS.push({
                        id_company: { _useMasterRecordId_: true },
                      });
                      this.setState({ record: R });
                    }}
                  >
                    + Add contact
                  </a>
                ) : null}
              </div>
            </div>

            <div className="card mt-4">
              <div className="card-header">Company Activities</div>
              <div className="card-body">
                <InputTable
                  {...this.getDefaultInputProps()}
                  model="CeremonyCrmApp/Modules/Core/Customers/Models/Activity"
                  value={R.ACTIVITIES}
                  onChange={(value: any) => {
                    this.updateRecord({ ACTIVITIES: value });
                  }}
                  columns={{
                    subject: { type: "varchar", title: "Subject" },
                    due_date: { type: "date", title: "Due Date" },
                    due_time: { type: "time", title: "Due Time" },
                    completed: { type: "boolean", title: "Completed" },
                  }}
                ></InputTable>
                {this.state.isInlineEditing ? (
                  <a
                    role="button"
                    onClick={() => {
                      if (!R.ACTIVITIES) R.ACTIVITIES = [];
                      R.ACTIVITIES.push({
                        id_company: { _useMasterRecordId_: true },
                      });
                      this.setState({ record: R });
                    }}
                  >
                    + Add activity
                  </a>
                ) : null}
              </div>
            </div>

            <div className="card mt-4">
              <div className="card-header">
                Contacts - FormInput with InputVarchar
              </div>
              <div className="card-body">
                {R.PERSONS ? R.PERSONS.map((item: any, key: number) => {
                  return (
                    <>
                      <FormInput>
                        <div className="flex">
                          <InputVarchar
                            {...this.getDefaultInputProps()}
                            value={item.first_name ?? ""}
                            placeholder={globalThis.app.translate(
                              "First name"
                            )}
                            onChange={(value: any) => {
                              this.updateRecord({
                                PERSONS: { [key]: { first_name: value } },
                              });
                            }}
                          ></InputVarchar>
                          <InputVarchar
                            {...this.getDefaultInputProps()}
                            value={item.last_name ?? ""}
                            placeholder={globalThis.app.translate(
                              "Last name"
                            )}
                            onChange={(value: any) => {
                              this.updateRecord({
                                PERSONS: { [key]: { last_name: value } },
                              });
                            }}
                          ></InputVarchar>
                        </div>
                      </FormInput>
                    </>
                  );
                })
                : globalThis.app.translate("No contacts")}
              </div>
            </div>
            <div className="card mt-4">
              <div className="card-header">Business Account</div>
              <div className="card-body">
                {R.BUSINESS_ACCOUNT ? (
                  <FormInput>
                    <div className="grid grid-cols-2 gap-4">
                      <label htmlFor="">Business Account Name</label>
                      <InputVarchar
                        {...this.getDefaultInputProps()}
                        value={R.BUSINESS_ACCOUNT.name ?? ""}
                        placeholder={globalThis.app.translate("Business Account Name")}
                        onChange={(value: any) => {
                          this.updateRecord({
                            BUSINESS_ACCOUNT: { name: value },
                          });
                        }}
                      ></InputVarchar>
                    </div>
                  </FormInput>
                ) : (
                  <a
                    role="button"
                    onClick={() => {
                      if (!R.BUSINESS_ACCOUNT) {
                        R.BUSINESS_ACCOUNT = {
                          id_company: { _useMasterRecordId_: true },
                        };
                      }
                      this.setState({ record: R });
                    }}
                  >
                    + Add Business Account
                  </a>
                )}
              </div>
            </div>
          </div>
          <div>
            <div className="card">
              <div className="card-header">this.state.record</div>
              <div className="card-body">
                <pre
                  style={{
                    color: "blue",
                    width: "100%",
                    fontFamily: "Courier New",
                    fontSize: "10px",
                  }}
                >
                  {JSON.stringify(R, null, 2)}
                </pre>
              </div>
            </div>
          </div>
        </div>
      </>
    );
  }
}