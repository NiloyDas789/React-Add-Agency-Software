import Input from '@/Components/Global/Input';
import InputError from '@/Components/Global/InputError';
import Label from '@/Components/Global/Label';
import Select from '@/Components/Global/Select';
import 'react-multiple-select-dropdown-lite/dist/index.css';
import MultiSelect from 'react-multiple-select-dropdown-lite';
import Authenticated from '@/Layouts/Authenticated';
import { useForm } from '@inertiajs/inertia-react';

export default function Index({ auth, clients, offers, stations, providers }) {
  const { data, setData, processing, errors, reset } = useForm({
    report_type_id: '1',
    offer_id: [],
    client_id: [],
    station_id: [],
    provider_id: [],
    start_date: '',
    end_date: '',
    year: [],
  });

  const years = ['2022', '2023', '2024', '2025', '2026', '2027', '2028', '2029', '2030', '2031'];

  const onHandleChange = (event) => {
    setData(event.target.name, event.target.value);
  };

  const onHandleSingleSelectChange = (key, value) => {
    setData(key, value);
  };

  const onHandleMSChange = (key, value) => {
    const arrValue = value ? value.split(',') : [];
    setData(key, arrValue);
  };

  const offerOptions = offers?.map((offer) => ({
    label: offer.offer,
    value: offer.id.toString(),
  }));
  const stationOptions = stations?.map((station) => ({
    label: station.title,
    value: station.id.toString(),
  }));
  const clientOptions = clients?.map((client) => ({
    label: client.name,
    value: client.id.toString(),
  }));
  const providerOptions = providers?.map((provider) => ({
    label: provider.name,
    value: provider.id.toString(),
  }));
  const yearOptions = years?.map((year) => ({
    label: year,
    value: year,
  }));
  const reportTypes = [
    { id: 1, value: 'Topline Call Reports' },
    { id: 2, value: 'Station Payable' },
    { id: 3, value: 'Station Report' },
    { id: 4, value: 'Creative by Station' },
    { id: 5, value: 'Offers and Payout' },
    { id: 6, value: 'Weekly Performance' },
    { id: 7, value: 'Data Provider' },
  ];

  return (
    <Authenticated auth={auth}>
      <div className="max-w-2xl mx-auto">
        <form>
          <div className="mt-4">
            <Label forInput="report_type" value="Report Type" />
            <Select
              name="report_type_id"
              className="mt-1 block w-full"
              handleChange={onHandleChange}
            >
              {reportTypes.map((report_type) => (
                <option value={report_type.id} key={report_type.id}>
                  {report_type.value}
                </option>
              ))}
            </Select>
            <InputError message={errors.report_type} className="mt-2" />
          </div>

          <div className="mt-4">
            <Label forInput="offer" value="Offers" />
            <MultiSelect
              name="offer"
              className="mt-1 block w-full"
              options={offerOptions}
              //   customValue="true"
              //   singleSelect="true"
              onChange={(value) => onHandleMSChange('offer_id', value)}
            />
            <InputError message={errors.offer} className="mt-2" />
          </div>
          <div className="mt-4">
            <Label forInput="provider" value="Providers" />
            <MultiSelect
              name="provider"
              className="mt-1 block w-full"
              options={providerOptions}
              customValue="true"
              //   singleSelect="true"
              onChange={(value) => onHandleMSChange('provider_id', value)}
            />
            <InputError message={errors.provider} className="mt-2" />
          </div>
          <div className="mt-4">
            <Label forInput="station_id" value="Station" />
            <MultiSelect
              name="station_id"
              className="mt-1 block w-full"
              options={stationOptions}
              customValue="true"
              //   singleSelect="true"
              onChange={(value) => onHandleMSChange('station_id', value)}
            />
            <InputError message={errors.station_id} className="mt-2" />
          </div>
          <div className="mt-4">
            <Label forInput="client_id" value="Clients" />
            <MultiSelect
              name="client_id"
              className="mt-1 block w-full"
              options={clientOptions}
              customValue="true"
              //   singleSelect="true"
              onChange={(value) => onHandleMSChange('client_id', value)}
            />
            <InputError message={errors.client_id} className="mt-2" />
          </div>

          <div className="mt-4">
            <Label forInput="start_date" value="Start Date" />
            <Input
              type="date"
              name="start_date"
              className="mt-1 block w-full"
              autoComplete="start_date"
              handleChange={onHandleChange}
            />
            <InputError message={errors.start_date} className="mt-2" />
          </div>

          <div className="mt-4">
            <Label forInput="end_date" value="End Date" />
            <Input
              type="date"
              name="end_date"
              className="mt-1 block w-full"
              autoComplete="end_date"
              handleChange={onHandleChange}
            />
            <InputError message={errors.end_date} className="mt-2" />
          </div>

          <div className="mt-4">
            <Label forInput="year" value="Year" />
            <MultiSelect
              name="year"
              className="mt-1 block w-full"
              options={yearOptions}
              customValue="true"
              //   singleSelect="true"
              onChange={(value) => onHandleMSChange('year', value)}
            />
            <InputError message={errors.year} className="mt-2" />
          </div>
        </form>
        <div className="flex items-center justify-end mt-4">
          <a
            href={route('all_reports.generate_report', { data })}
            className="inline-flex gap-1 items-center px-3 py-2 bg-slate-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-slate-600 transition ease-in-out duration-150"
            target="_blank"
          >
            Generate
          </a>
        </div>
      </div>
    </Authenticated>
  );
}
