import Heading from '@/Components/Global/Heading';
import Authenticated from '@/Layouts/Authenticated';
import { useForm } from '@inertiajs/inertia-react';
import { useEffect } from 'react';
import toast from 'react-hot-toast';
import Form from './Form';
import StationCreate from '@/Pages/Stations/Create';
import TFNCreate from '@/Pages/TFNs/Create';

export default function Create({ auth, clients, offers, stations, tollFreeNumbers, states }) {
  const { data, setData, post, processing, errors, reset } = useForm({
    client_id: '',
    offer: '',
    creative: '',
    toll_free_number_id: '',
    station_id: '',
    lead_sku: '',
    state: '',
    length: '',
    master: '',
    ad_id: '',
    source_type: '',
    website: '',
    terminating_number: '',
    data_type: '',
    assigned_at: '',
    start_at: '',
    end_at: '',
    test_call_at: '',
    updateTfn: false,
  });

  useEffect(() => {
    return () => {
      reset();
    };
  }, []);

  const submit = (e) => {
    e.preventDefault();

    post(route('offerTollFreeNumbers.store'), {
      preserveScroll: true,
      onSuccess: () => {
        reset();
        toast.success('OfferTFN created successfully.');
      },
      onError: () => {
        toast.error('Something went wrong!');
      },
    });
  };

  return (
    <Authenticated auth={auth}>
      <div className="max-w-2xl mx-auto">
        <Heading className="mb-6">TFN Assignment</Heading>

        <div className="flex flex-wrap gap-x-4">
          <StationCreate />
          <TFNCreate />
        </div>

        <Form
          data={data}
          setData={setData}
          submit={submit}
          errors={errors}
          processing={processing}
          clients={clients}
          offers={offers}
          stations={stations}
          states={states}
          tollFreeNumbers={tollFreeNumbers}
          message={errors[0]}
        />
      </div>
    </Authenticated>
  );
}
