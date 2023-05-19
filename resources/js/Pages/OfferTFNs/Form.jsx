import Button from '@/Components/Global/Button';
import Input from '@/Components/Global/Input';
import InputError from '@/Components/Global/InputError';
import Label from '@/Components/Global/Label';
import Select from '@/Components/Global/Select';
import { useEffect, useState } from 'react';
import 'react-multiple-select-dropdown-lite/dist/index.css';
import MultiSelect from 'react-multiple-select-dropdown-lite';
import { v4 as uuid } from 'uuid';

export default function Form({
  data,
  setData,
  submit,
  errors,
  processing,
  clients,
  offers,
  stations,
  states,
  tollFreeNumbers,
  isUpdating = 0,
  message,
}) {
  const [dependentOffers, setDependentOffers] = useState([]);
  const [dependentCreatives, setDependentCreatives] = useState([]);
  const [dependentLengths, setDependentLengths] = useState([]);

  const onHandleChange = (event) => {
    const { name, value } = event.target;
    const dependentData = ['client_id', 'offer', 'creative'];
    if (dependentData.includes(name)) {
      setData((prevData) => ({ ...prevData, length: '', [name]: value }));
    } else {
      setData(name, value);
    }
  };

  useEffect(() => {
    setDependentOffers([]);
    setDependentCreatives([]);
    setDependentLengths([]);
    if (data.client_id === '') return;

    offers.map((offer) => {
      if (offer.client_id == data.client_id) {
        setDependentOffers((prev) => [...prev, offer.offer]);
      }
    });
  }, [data.client_id]);

  useEffect(() => {
    setDependentCreatives([]);
    setDependentLengths([]);
    if (data.offer === '') return;

    offers.map((offer) => {
      if (offer.offer == data.offer) {
        setDependentCreatives((prev) => [...prev, offer.creative]);
      }
    });
  }, [data.offer]);

  const onHandleSingleSelectChange = (key, value) => {
    setData(key, value);
  };

  const tollFreeNumberOptions = tollFreeNumbers?.map((tollFreeNumber) => ({
    label: tollFreeNumber.number,
    value: tollFreeNumber.id.toString(),
  }));

  const stateOptions = states?.map((state) => ({
    label: state,
    value: state,
  }));

  const stationOptions = stations?.map((station) => ({
    label: station.title,
    value: station.id.toString(),
  }));

  const lengthOptions = () => {
    const creativeOffer = offers.find((offer) => offer.creative === data.creative);
    if (creativeOffer) {
      return creativeOffer.offer_lengths.map(({ length }) => ({
        label: length.toString(),
        value: length.toString(),
      }));
    }
    return [];
  };

  return (
    <form onSubmit={submit}>
      <div>
        <Label forInput="client" value="Client" required />
        <Select
          name="client_id"
          value={data.client_id}
          className="mt-1 block w-full"
          handleChange={onHandleChange}
          required
        >
          <option value="">Select client</option>
          {clients?.map((client) => (
            <option value={client.id} key={uuid()}>
              {client.name}
            </option>
          ))}
        </Select>
        <InputError message={errors.client_id} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="offer" value="Offer" required />
        <Select
          name="offer"
          value={data.offer}
          className="mt-1 block w-full"
          handleChange={onHandleChange}
          required
        >
          <option value="">Select offer</option>
          {dependentOffers?.map((offer) => (
            <option value={offer} key={uuid()}>
              {offer}
            </option>
          ))}
        </Select>
        <InputError message={errors.offer} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="creative" value="Creative" required />
        <Select
          name="creative"
          value={data.creative}
          className="mt-1 block w-full"
          handleChange={onHandleChange}
          required
        >
          <option value="">Select creative</option>
          {dependentCreatives?.map((creative) => (
            <option value={creative} key={uuid()}>
              {creative}
            </option>
          ))}
        </Select>
        <InputError message={errors.creative} className="mt-2" />
      </div>

      <div className="md:grid md:grid-cols-2 md:gap-4">
        <div className="mt-4">
          <Label forInput="state" value="State" required />
          <MultiSelect
            name="state"
            className="mt-1 block w-full"
            defaultValue={data.state}
            singleSelect={data.updateTfn == true ? true : false}
            options={stateOptions}
            onChange={(value) => onHandleSingleSelectChange('state', value)}
            required
          />
          <InputError message={errors.state} className="mt-2" />
        </div>

        <div className="mt-4">
          <Label forInput="source_type" value="Source Type" required />
          <Select
            name="source_type"
            value={data.source_type}
            className="mt-1 block w-full"
            handleChange={onHandleChange}
            required
          >
            <option value="">Select Source Type</option>
            <option value="1">Exclusive</option>
            <option value="2">Shared</option>
          </Select>
          <InputError message={errors.source_type} className="mt-2" />
        </div>
      </div>

      <div className="md:grid md:grid-cols-3 md:gap-4">
        <div className="mt-4">
          <Label forInput="station" value="Station" required />
          <MultiSelect
            name="station_id"
            className="mt-1 block w-full"
            defaultValue={data.station_id}
            singleSelect="true"
            options={stationOptions}
            onChange={(value) => onHandleSingleSelectChange('station_id', value)}
            required
          />
          <InputError message={errors.station_id} className="mt-2" />
        </div>

        <div className="mt-4">
          <Label forInput="toll_free_number_id" value="Toll Free Number" required />
          <MultiSelect
            name="toll_free_number_id"
            defaultValue={data.toll_free_number_id}
            className="mt-1 block w-full"
            options={tollFreeNumberOptions}
            singleSelect="true"
            onChange={(value) => onHandleSingleSelectChange('toll_free_number_id', value)}
            required
          />
          <InputError message={errors.toll_free_number_id} className="mt-2" />
        </div>

        <div className="mt-4">
          <Label forInput="lead_sku" value="LeadSKU/Website/Terminating" />
          <Input
            type="text"
            name="lead_sku"
            value={data.lead_sku}
            className="mt-1 block w-full"
            autoComplete="lead_sku"
            handleChange={onHandleChange}
          />
          <InputError message={errors.lead_sku} className="mt-2" />
        </div>
      </div>

      <div className="md:grid md:grid-cols-3 md:gap-4">
        <div className="mt-4">
          <Label forInput="length" value="Length" required />
          <MultiSelect
            name="length"
            options={lengthOptions()}
            defaultValue={data.length}
            className="mt-1 block w-full"
            onChange={(value) => onHandleSingleSelectChange('length', value.toString())}
            required
          />
          <InputError message={errors.length} className="mt-2" />
        </div>

        <div className="mt-4">
          <Label forInput="master" value="Master" required />
          <Input
            type="text"
            name="master"
            value={data.master}
            className="mt-1 block w-full"
            autoComplete="master"
            handleChange={onHandleChange}
            required
          />
          <InputError message={errors.master} className="mt-2" />
        </div>

        <div className="mt-4">
          <Label forInput="ad_id" value="Ad_id" required />
          <Input
            type="text"
            name="ad_id"
            value={data.ad_id}
            className="mt-1 block w-full"
            autoComplete="ad_id"
            handleChange={onHandleChange}
            required
          />
          <InputError message={errors.ad_id} className="mt-2" />
        </div>
      </div>

      <div>
        <div className="mt-4">
          <Label forInput="website" value="Website" />
          <Input
            type="text"
            name="website"
            value={data.website}
            className="mt-1 block w-full"
            autoComplete="website"
            handleChange={onHandleChange}
          />
          <InputError message={errors.website} className="mt-2" />
        </div>
      </div>
      <div className="md:grid md:grid-cols-2 md:gap-4">
        <div className="mt-4">
          <Label forInput="terminating_number" value="Terminating Number" />
          <Input
            type="text"
            name="terminating_number"
            value={data.terminating_number}
            className="mt-1 block w-full"
            autoComplete="terminating_number"
            handleChange={onHandleChange}
          />
          <InputError message={errors.terminating_number} className="mt-2" />
        </div>

        <div className="mt-4">
          <Label forInput="data_type" value="Data Type" required />
          <Select
            name="data_type"
            value={data.data_type}
            className="mt-1 block w-full"
            handleChange={onHandleChange}
            required
          >
            <option value="">Select Data Type</option>
            <option value="1">TFN</option>
            <option value="2">WEB</option>
            <option value="3">TFN and WEB</option>
          </Select>
          <InputError message={errors.data_type} className="mt-2" />
        </div>
      </div>

      <div className="md:grid md:grid-cols-2 md:gap-4">
        <div className="mt-4">
          <Label forInput="assigned_at" value="Assigned Date" required />
          <Input
            type="date"
            name="assigned_at"
            value={data.assigned_at}
            className="mt-1 block w-full"
            autoComplete="assigned_at"
            handleChange={onHandleChange}
            required
          />
          <InputError message={errors.assigned_at} className="mt-2" />
        </div>

        <div className="mt-4">
          <Label forInput="start_at" value="Start Date" required />
          <Input
            type="date"
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
          <Label forInput="end_at" value="End Date" />
          <Input
            type="date"
            name="end_at"
            value={data.end_at}
            className="mt-1 block w-full"
            autoComplete="end_at"
            handleChange={onHandleChange}
          />
          <InputError message={errors.end_at} className="mt-2" />
        </div>

        <div className="mt-4">
          <Label forInput="test_call_at" value="Test Date" />
          <Input
            type="date"
            name="test_call_at"
            value={data.test_call_at}
            className="mt-1 block w-full"
            autoComplete="test_call_at"
            handleChange={onHandleChange}
          />
          <InputError message={errors.test_call_at} className="mt-2" />
        </div>
      </div>

      <div className="flex items-center justify-end mt-4">
        <InputError message={message}></InputError>
        <Button className="ml-4" processing={processing}>
          {isUpdating ? 'Update' : 'Create'}
        </Button>
      </div>
      <div className="max-w rounded overflow-hidden mt-4 border border-gray-300">
        <div className="px-6 py-4">
          <div className="font-bold text-sm mb-2">Notes:</div>

          <ul className="list-disc pl-4">
            <li>
              <p className="text-gray-700 text-sm">
                Here, the end date of the same exclusive TFN can not be empty value multiple times
                at a time, only one row should have empty end date and all the other same TFN must
                have end date with start date. Same rules for shared TFN with containing same state.
              </p>
            </li>
            <li>
              <p className="text-gray-700 text-sm">
                Between a start to end date there must have only one TFN. For example: In the range
                of 01-01-2023 to 02-01-2023 if there is a TFN then you can't create another TFN in
                that range.
              </p>
            </li>
            <li>
              <p className="text-gray-700 text-sm">
                If a TFN assignment's end date is empty or current date is between the rang of TFN's
                start date to end date then import file will map otherwise no mapping will happen.
                For example: if you insert an end date that is from previous day, week, month or
                year then the mapping will not happen. The end date must be greater than today.
              </p>
            </li>
          </ul>
        </div>
      </div>
    </form>
  );
}
