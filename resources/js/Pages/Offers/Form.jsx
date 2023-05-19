import Button from '@/Components/Global/Button';
import Input from '@/Components/Global/Input';
import InputError from '@/Components/Global/InputError';
import Label from '@/Components/Global/Label';
import Select from '@/Components/Global/Select';
import { ceil, floor, round } from 'lodash';
import MultiSelect from 'react-multiple-select-dropdown-lite';
import 'react-multiple-select-dropdown-lite/dist/index.css';

export default function Form({
  data,
  setData,
  submit,
  errors,
  processing,
  clients,
  providers,
  qualifications,
  states,
  dispositions,
  isUpdating = 0,
  message,
}) {
  const onHandleMSChange = (key, value) => {
    const arrValue = value ? value.split(',') : [];
    setData(key, arrValue);
  };

  const onHandleDispositionChange = (key, value) => {
    setData(key, value);
  };

  const onHandleChange = (event) => {
    setData(event.target.name, event.target.value);
  };

  const qualificationOptions = qualifications?.map((qualification) => ({
    label: qualification.title,
    value: qualification.id.toString(),
  }));

  const stateOptions = states?.map((state) => ({
    label: state.name,
    value: state.id.toString(),
  }));

  const dispositionOptions = dispositions?.map((disposition) => ({
    label: disposition.title,
    value: disposition.title.toString(),
  }));

  return (
    <form onSubmit={submit}>
      <div>
        <Label forInput="offer" value="Offer" required />
        <Input
          type="text"
          name="offer"
          value={data.offer}
          className="mt-1 block w-full"
          autoComplete="offer"
          isFocused={true}
          handleChange={onHandleChange}
          required
        />
        <InputError message={errors.offer} className="mt-2" />
      </div>
      <div className="md:grid md:grid-cols-2 md:gap-4 mt-4">
        <div>
          <Label forInput="creative" value="Creative" />
          <Input
            type="text"
            name="creative"
            value={data.creative}
            className="mt-1 block w-full"
            autoComplete="creative"
            handleChange={onHandleChange}
            required
          />
          <InputError message={errors.creative} className="mt-2" />
        </div>

        <div>
          <Label forInput="lengths" value="Length" required />
          <MultiSelect
            name="lengths"
            defaultValue={data.lengths}
            className="mt-1 block w-full"
            placeholder="Add Length"
            customValue="true"
            onChange={(value) => onHandleMSChange('lengths', value)}
            required
          />
          <InputError message={errors.lengths} className="mt-2" />
        </div>
      </div>

      <div className="md:grid md:grid-cols-2 md:gap-4">
        <div className="mt-4">
          <Label forInput="client" value="Client" required />
          <Select
            name="client_id"
            value={data.client_id}
            className="mt-1 block w-full"
            handleChange={onHandleChange}
            required
          >
            <option value="">Select Client</option>
            {clients?.map((client) => (
              <option value={client.id} key={client.id}>
                {client.name}
              </option>
            ))}
          </Select>
          <InputError message={errors.client_id} className="mt-2" />
        </div>

        <div className="mt-4">
          <Label forInput="provider" value="Provider" required />
          <Select
            name="provider_id"
            value={data.provider_id}
            className="mt-1 block w-full"
            handleChange={onHandleChange}
            required
          >
            <option value="">Select Provider</option>
            {providers?.map((provider) => (
              <option value={provider.id} key={provider.id}>
                {provider.name}
              </option>
            ))}
          </Select>
          <InputError message={errors.provider_id} className="mt-2" />
        </div>
      </div>

      <div className="md:grid md:grid-cols-3 md:gap-4">
        <div className="mt-4">
          <Label forInput="billable_payout" value="Billable Payout" required />
          <Input
            type="number"
            name="billable_payout"
            value={data.billable_payout}
            className="mt-1 block w-full"
            autoComplete="billable_payout"
            handleChange={onHandleChange}
          />
          <InputError message={errors.billable_payout} className="mt-2" />
        </div>

        <div className="mt-4">
          <Label forInput="media_payout" value="Media Payout" required />
          <Input
            type="number"
            name="media_payout"
            value={data.media_payout}
            className="mt-1 block w-full"
            autoComplete="media_payout"
            handleChange={onHandleChange}
            required
          />
          <InputError message={errors.media_payout} className="mt-2" />
        </div>

        <div className="mt-4">
          <Label forInput="margin" value="Margin" required />
          <Input
            type="text"
            name="margin"
            value={
              data.billable_payout != '' || data.billable_payout != 0
                ? parseFloat(
                    ((data.billable_payout - data.media_payout) / data.billable_payout) * 100
                  ).toFixed(2) + '%'
                : ''
            }
            className="mt-1 block w-full"
            readOnly={true}
          />
          <InputError message={errors.margin} className="mt-2" />
        </div>
      </div>

      <div className="mt-4">
        <Label forInput="qualification_ids" value="Qualifications" required />
        <MultiSelect
          name="qualification_ids"
          options={qualificationOptions}
          defaultValue={data.qualification_ids}
          onChange={(value) => onHandleMSChange('qualification_ids', value)}
          placeholder="Select qualifications"
          className="mt-1"
        />
        <InputError message={errors.qualification_ids} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="dispositions" value="Disposition" required />
        <MultiSelect
          type="text"
          name="dispositions"
          options={dispositionOptions}
          defaultValue={data.dispositions}
          className="mt-1 block w-full"
          placeholder="Select disposition"
          onChange={(value) => onHandleDispositionChange('dispositions', value)}
        />
        <InputError message={errors.disposition} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="state_ids" value="Restricted States" />
        <MultiSelect
          name="state_ids"
          options={stateOptions}
          defaultValue={data.state_ids}
          onChange={(value) => onHandleMSChange('state_ids', value)}
          placeholder="Select states"
          className="mt-1"
        />
        <InputError message={errors.state_ids} className="mt-2" />
      </div>

      <div className="md:grid md:grid-cols-2 md:gap-4">
        <div className="mt-4">
          <Label forInput="billable_call_duration" value="Billable Call Duration" required />
          <Input
            type="number"
            name="billable_call_duration"
            value={data.billable_call_duration}
            className="mt-1 block w-full"
            autoComplete="billable_call_duration"
            handleChange={onHandleChange}
          />
          <InputError message={errors.billable_call_duration} className="mt-2" />
        </div>

        <div className="mt-4">
          <Label forInput="de_dupe" value="De_dupe" required />
          <Input
            type="number"
            name="de_dupe"
            value={data.de_dupe}
            className="mt-1 block w-full"
            autoComplete="de_dupe"
            handleChange={onHandleChange}
          />
          <InputError message={errors.de_dupe} className="mt-2" />
        </div>
      </div>

      <div className="md:grid md:grid-cols-2 md:gap-4">
        <div className="mt-4">
          <Label forInput="start_at" value="Start At" required />
          <Input
            type="Date"
            name="start_at"
            value={data.start_at}
            className="mt-1 block w-full"
            autoComplete="start_at"
            handleChange={onHandleChange}
            required
          />
          <InputError message={errors.start_at} className="mt-2" />
        </div>

        <div className="mt-4">
          <Label forInput="end_at" value="End At" required />
          <Input
            type="Date"
            name="end_at"
            value={data.end_at}
            className="mt-1 block w-full"
            autoComplete="end_at"
            handleChange={onHandleChange}
            required
          />
          <InputError message={errors.end_at} className="mt-2" />
        </div>
      </div>

      <div className="flex items-center justify-end mt-4">
        <InputError message={message}></InputError>
        <Button className="ml-4" processing={processing}>
          {isUpdating ? 'Update' : 'Create'}
        </Button>
      </div>
    </form>
  );
}
