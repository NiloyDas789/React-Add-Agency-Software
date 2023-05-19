import Heading from '@/Components/Global/Heading';
import Authenticated from '@/Layouts/Authenticated';
import { useForm } from '@inertiajs/inertia-react';
import { useEffect } from 'react';
import toast from 'react-hot-toast';
import Form from './Form';
import UserCreate from '@/Pages/Users/Create';
import ProviderCreate from '@/Pages/Providers/Create';
import QualificationCreate from '@/Pages/Qualifications/Create';
import StateCreate from '@/Pages/States/Create';
import DispositionCreate from '@/Pages/Disposition/Create';
import InputError from '@/Components/Global/InputError';

export default function Create({
  auth,
  clients,
  providers,
  qualifications,
  states,
  dispositions,
  offerLengths,
}) {
  const { data, setData, post, processing, errors, reset } = useForm({
    client_id: '',
    provider_id: '',
    qualification_ids: [],
    state_ids: [],
    offer: '',
    creative: '',
    lengths: [],
    billable_payout: '',
    media_payout: '',
    margin: '',
    dispositions: '',
    billable_call_duration: '',
    de_dupe: '',
    start_at: '',
    end_at: '',
  });

  useEffect(() => {
    return () => {
      reset();
    };
  }, []);

  const submit = (e) => {
    e.preventDefault();

    post(route('offers.store'), {
      preserveScroll: true,
      onSuccess: () => {
        reset();
        toast.success('Offer created successfully.');
      },
      onError: () => {
        toast.error('Something went wrong');
      },
    });
  };

  return (
    <Authenticated auth={auth}>
      <div className="max-w-2xl mx-auto">
        <Heading className="mb-6">Create Offer</Heading>

        <div className="flex flex-wrap gap-x-4">
          <UserCreate />
          <ProviderCreate />
          <QualificationCreate />
          <StateCreate />
          <DispositionCreate />
        </div>
        <Form
          data={data}
          setData={setData}
          submit={submit}
          errors={errors}
          processing={processing}
          clients={clients}
          providers={providers}
          qualifications={qualifications}
          states={states}
          dispositions={dispositions}
          offerLengths={offerLengths}
          message={errors[0]}
        />
      </div>
    </Authenticated>
  );
}
