import { z } from 'zod';

export const reviewSchema = z.object({
    rating: z.number().min(1, { message: 'Rating is obligatory' }).max(5, { message: '5 stars is the maximum' }),

    description: z.string().min(1, { message: 'Description is obligatory' }).max(2000, {
        message: 'Description have to be maximum 2000 caracters'
    })
});

export type ReviewFormValues = z.infer<typeof reviewSchema>;
